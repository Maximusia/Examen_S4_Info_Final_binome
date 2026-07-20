# TODO complète — Version 2 Mobile Money

Cette version part des règles suivantes afin d’éviter toute ambiguïté.

## Règles métier retenues

### 1. Commission vers un autre opérateur

Le pourcentage est appliqué **aux frais de transfert**, et non au montant envoyé.

```text
Commission externe = frais de transfert × pourcentage / 100
```

Exemple avec une commission configurée à **2 %** :

```text
Montant envoyé                    : 100 000 Ar
Frais normaux de transfert        :     800 Ar
Commission externe                : 800 × 2 %
Commission externe                :      16 Ar
Total des frais                   :     816 Ar
Total débité                      : 100 816 Ar
```

Répartition :

```text
Gain de notre opérateur           : 800 Ar
Commission due à l’autre opérateur:  16 Ar
```

La commission ne doit donc pas être mélangée avec les frais normaux dans la base.

### 2. Transfert interne

```text
033 → 037
```

* aucune commission externe ;
* le destinataire doit exister dans `users` ;
* son solde est crédité ;
* l’opérateur conserve les frais de transfert.

### 3. Transfert externe

```text
033 → 032
```

* une commission est ajoutée aux frais ;
* le destinataire ne doit pas obligatoirement exister dans `users` ;
* aucun compte local n’est crédité ;
* le transfert est enregistré comme montant à régler à l’autre opérateur.

### 4. Inclure les frais de retrait

Cette option est proposée **dans le formulaire de transfert**, et non dans le formulaire de retrait.

Exemple :

```text
Montant souhaité par destinataire : 100 000 Ar
Frais de retrait                  :     800 Ar
Frais de transfert                :     800 Ar
Commission externe à 2 %          :      16 Ar
```

Sans inclusion :

```text
Somme transférée au destinataire  : 100 000 Ar
Total débité                      : 100 816 Ar
```

Avec inclusion :

```text
Somme transférée au destinataire  : 100 800 Ar
Total débité                      : 101 616 Ar
```

Le destinataire reçoit `100 800 Ar`. Après un retrait coûtant `800 Ar`, il conserve réellement `100 000 Ar`.

---

# Phase 1 — Modification de la base de données

## 1.1 Créer la table `operators`

* [ ] Ajouter la table qui représente notre opérateur et les opérateurs externes.
* [ ] Ajouter un nom pour chaque opérateur.
* [ ] Identifier notre propre opérateur.
* [ ] Ajouter le pourcentage de commission appliqué aux frais de transfert.
* [ ] Empêcher les pourcentages négatifs.

```sql
CREATE TABLE IF NOT EXISTS operators (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,

    is_own_operator INTEGER NOT NULL DEFAULT 0
        CHECK (is_own_operator IN (0, 1)),

    commission_percent REAL NOT NULL DEFAULT 0
        CHECK (commission_percent >= 0),

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

Le pourcentage peut être configuré séparément pour chaque opérateur externe.

Exemple :

```sql
INSERT INTO operators (
    name,
    is_own_operator,
    commission_percent
) VALUES
('Notre opérateur', 1, 0),
('Opérateur 032', 0, 2),
('Opérateur 031', 0, 2);
```

---

## 1.2 Modifier la table `prefixes`

* [ ] Retirer l’idée du simple champ `is_operator`.
* [ ] Associer chaque préfixe à un véritable opérateur avec `operator_id`.
* [ ] Permettre à un opérateur de posséder plusieurs préfixes.
* [ ] Garder le préfixe unique.

```sql
CREATE TABLE IF NOT EXISTS prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operator_id INTEGER NOT NULL,
    prefix TEXT NOT NULL UNIQUE,

    FOREIGN KEY (operator_id)
        REFERENCES operators(id)
        ON DELETE CASCADE
);
```

Données de départ :

```sql
INSERT INTO prefixes (operator_id, prefix) VALUES
(1, '033'),
(1, '037'),
(2, '032'),
(3, '031');
```

---

## 1.3 Modifier la table `transactions`

Ajouter les informations nécessaires pour distinguer les transferts internes et externes.

* [ ] Ajouter `receiver_phone`.
* [ ] Rendre `receiver_user_id` nullable.
* [ ] Ajouter `receiver_operator_id`.
* [ ] Ajouter `base_fee`.
* [ ] Ajouter `external_commission`.
* [ ] Ajouter `withdrawal_fee_included`.
* [ ] Ajouter `included_withdrawal_fee`.
* [ ] Ajouter `batch_reference` pour regrouper un envoi multiple.
* [ ] Conserver `fee` comme total des frais, ou le remplacer par `total_fee`.
* [ ] Ajouter des contraintes sur les montants et statuts.

Structure recommandée :

```sql
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    user_id INTEGER NOT NULL,
    operation_type_id INTEGER NOT NULL,

    receiver_user_id INTEGER NULL,
    receiver_phone TEXT NULL,
    receiver_operator_id INTEGER NULL,

    amount INTEGER NOT NULL
        CHECK (amount > 0),

    base_fee INTEGER NOT NULL DEFAULT 0
        CHECK (base_fee >= 0),

    external_commission INTEGER NOT NULL DEFAULT 0
        CHECK (external_commission >= 0),

    included_withdrawal_fee INTEGER NOT NULL DEFAULT 0
        CHECK (included_withdrawal_fee >= 0),

    total_fee INTEGER NOT NULL DEFAULT 0
        CHECK (total_fee >= 0),

    withdrawal_fee_included INTEGER NOT NULL DEFAULT 0
        CHECK (withdrawal_fee_included IN (0, 1)),

    is_external INTEGER NOT NULL DEFAULT 0
        CHECK (is_external IN (0, 1)),

    batch_reference TEXT NULL,

    status TEXT NOT NULL DEFAULT 'completed'
        CHECK (
            status IN (
                'pending',
                'completed',
                'failed',
                'cancelled'
            )
        ),

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(id),

    FOREIGN KEY (operation_type_id)
        REFERENCES operation_types(id),

    FOREIGN KEY (receiver_user_id)
        REFERENCES users(id),

    FOREIGN KEY (receiver_operator_id)
        REFERENCES operators(id)
);
```

---

## 1.4 Mettre à jour `base.sql`

* [ ] Ajouter la table `operators`.
* [ ] Recréer la table `prefixes` avec `operator_id`.
* [ ] Recréer ou modifier `transactions`.
* [ ] Insérer notre opérateur.
* [ ] Insérer les opérateurs externes.
* [ ] Associer les préfixes aux opérateurs.
* [ ] Conserver les types d’opérations et les barèmes de la V1.
* [ ] Supprimer puis recréer `database.db` si nécessaire.
* [ ] Réexécuter entièrement `base.sql`.
* [ ] Vérifier les clés étrangères avec :

```sql
PRAGMA foreign_keys = ON;
```

---

# Phase 2 — Models

## 2.1 Créer `OperatorModel.php`

* [ ] Associer le modèle à la table `operators`.
* [ ] Déclarer les champs autorisés.
* [ ] Créer `getOwnOperator()`.
* [ ] Créer `getExternalOperators()`.
* [ ] Créer `findOperatorById($id)`.
* [ ] Créer `createExternalOperator($data)`.
* [ ] Créer `updateExternalOperator($id, $data)`.
* [ ] Créer `deleteExternalOperator($id)`.
* [ ] Créer `updateCommissionPercent($operatorId, $percent)`.
* [ ] Vérifier qu’on ne supprime pas notre propre opérateur.
* [ ] Vérifier que la commission est supérieure ou égale à zéro.

Champs :

```php
protected $allowedFields = [
    'name',
    'is_own_operator',
    'commission_percent',
];
```

---

## 2.2 Modifier `PrefixModel.php`

* [ ] Ajouter `operator_id` dans les champs autorisés.
* [ ] Créer `getPrefixesByOperator($operatorId)`.
* [ ] Créer `getOwnOperatorPrefixes()`.
* [ ] Créer `getExternalOperatorPrefixes()`.
* [ ] Créer `findPrefixByPhone($phone)`.
* [ ] Créer `findOperatorByPhone($phone)`.
* [ ] Créer `isOwnOperatorPhone($phone)`.
* [ ] Créer `isExternalOperatorPhone($phone)`.
* [ ] Créer `prefixExists($prefix)`.
* [ ] Créer `addPrefixToOperator($operatorId, $prefix)`.
* [ ] Créer `deletePrefix($id)`.
* [ ] Vérifier que le préfixe contient uniquement trois chiffres.
* [ ] Empêcher les doublons.

---

## 2.3 Modifier `TransactionModel.php`

* [ ] Ajouter tous les nouveaux champs dans `$allowedFields`.
* [ ] Créer `createTransferTransaction($data)`.
* [ ] Créer `getInternalTransferFees()`.
* [ ] Créer `getExternalTransferBaseFees()`.
* [ ] Créer `getExternalCommissions()`.
* [ ] Créer `getAmountsByExternalOperator()`.
* [ ] Créer `getSettlementByExternalOperator()`.
* [ ] Créer `getTransactionsByBatch($batchReference)`.
* [ ] Adapter l’historique client pour afficher les transferts envoyés et reçus.
* [ ] Afficher le numéro externe même lorsque `receiver_user_id` est null.

La situation par opérateur devra fournir :

```text
Nom de l’opérateur
Montant total transféré
Commission totale
Total à régler
```

Calcul :

```text
Total à régler =
montants transférés + commissions externes
```

---

## 2.4 Modifier `FeeRuleModel.php`

* [ ] Conserver la recherche des frais de transfert selon le montant.
* [ ] Conserver la recherche des frais de retrait.
* [ ] Créer ou vérifier `getFeeByCodeAndAmount($code, $amount)`.
* [ ] Retourner une erreur si aucun barème ne correspond au montant.
* [ ] Ne pas retourner silencieusement zéro lorsqu’un montant est hors barème.

---

## 2.5 Modifier `UserModel.php`

* [ ] Conserver `findByPhone($phone)`.
* [ ] Conserver `updateBalance($userId, $newBalance)`.
* [ ] Ajouter une méthode de débit sécurisée.
* [ ] Ajouter une méthode de crédit sécurisée.
* [ ] Ne créer automatiquement un utilisateur que pour un préfixe interne.
* [ ] Ne pas créer de faux utilisateurs pour les numéros externes.

---

# Phase 3 — Services métier

Il est préférable de placer les calculs dans des services plutôt que dans les vues ou les contrôleurs.

## 3.1 Créer `TransferFeeService.php`

Emplacement :

```text
app/Libraries/TransferFeeService.php
```

* [ ] Récupérer les frais normaux de transfert.
* [ ] Identifier l’opérateur du destinataire.
* [ ] Déterminer si le transfert est interne ou externe.
* [ ] Récupérer le pourcentage de commission.
* [ ] Appliquer le pourcentage uniquement aux frais de transfert.
* [ ] Arrondir la commission à l’ariary entier.
* [ ] Calculer le total des frais.
* [ ] Retourner le détail complet du calcul.

Calcul retenu :

```php
$externalCommission = (int) round(
    $baseFee * $commissionPercent / 100
);
```

Résultat recommandé :

```php
[
    'base_fee'              => 800,
    'commission_percent'    => 2,
    'external_commission'   => 16,
    'total_transfer_fee'    => 816,
    'is_external'           => true,
    'receiver_operator_id'  => 2,
];
```

Pour un transfert interne :

```php
[
    'base_fee'              => 800,
    'commission_percent'    => 0,
    'external_commission'   => 0,
    'total_transfer_fee'    => 800,
    'is_external'           => false,
    'receiver_operator_id'  => 1,
];
```

---

## 3.2 Créer `TransferService.php`

* [ ] Recevoir le client expéditeur.
* [ ] Recevoir la liste des destinataires.
* [ ] Recevoir le montant total à partager.
* [ ] Recevoir l’option d’inclusion des frais de retrait.
* [ ] Nettoyer les numéros.
* [ ] Supprimer les doublons.
* [ ] Vérifier qu’au moins un numéro est présent.
* [ ] Vérifier que l’expéditeur ne figure pas parmi les destinataires.
* [ ] Identifier l’opérateur de chaque destinataire.
* [ ] Vérifier les destinataires internes dans `users`.
* [ ] Ne pas exiger l’existence locale des destinataires externes.
* [ ] Diviser exactement le montant.
* [ ] Calculer les frais de transfert de chaque destination.
* [ ] Calculer les frais de retrait inclus si la case est cochée.
* [ ] Calculer le débit total.
* [ ] Vérifier le solde avant toute modification.
* [ ] Exécuter toutes les opérations dans une transaction SQL.
* [ ] Débiter une seule fois le montant global de l’expéditeur.
* [ ] Créditer uniquement les comptes internes.
* [ ] Enregistrer une transaction par destinataire.
* [ ] Utiliser une référence commune pour l’envoi multiple.
* [ ] Tout annuler si une opération échoue.

---

# Phase 4 — Répartition du montant multiple

## 4.1 Division exacte

Pour `1 000 Ar` envoyé à trois personnes :

```text
Destinataire 1 : 334 Ar
Destinataire 2 : 333 Ar
Destinataire 3 : 333 Ar
```

* [ ] Utiliser `intdiv()` pour obtenir la partie commune.
* [ ] Calculer le reste avec `%`.
* [ ] Distribuer un ariary supplémentaire aux premiers destinataires.
* [ ] Vérifier que la somme distribuée correspond exactement au montant saisi.
* [ ] Ne jamais perdre le reste.
* [ ] Ne jamais débiter un montant qui n’a pas été distribué.

Exemple :

```php
$baseAmount = intdiv($totalAmount, $receiverCount);
$remainder  = $totalAmount % $receiverCount;

foreach ($receivers as $index => $receiver) {
    $receiverAmount = $baseAmount;

    if ($index < $remainder) {
        $receiverAmount++;
    }
}
```

---

# Phase 5 — Inclusion des frais de retrait

## 5.1 Calcul par destinataire

* [ ] Ajouter la case dans le formulaire de transfert.
* [ ] Calculer les frais de retrait sur la part de chaque destinataire.
* [ ] Ajouter ces frais au montant crédité ou envoyé.
* [ ] Conserver séparément le montant demandé et les frais de retrait inclus.
* [ ] Enregistrer l’option dans chaque transaction.
* [ ] Ne pas appliquer automatiquement cette option si la case n’est pas cochée.

Exemple pour une part de `100 000 Ar` :

```text
Montant demandé                 : 100 000 Ar
Frais de retrait                :     800 Ar
Montant réellement envoyé       : 100 800 Ar
```

## 5.2 Débit total d’un transfert

Pour chaque destinataire :

```text
Débit correspondant =
part demandée
+ frais de retrait inclus
+ frais normaux de transfert
+ commission externe
```

La commission externe reste calculée uniquement sur les frais normaux de transfert :

```text
Commission externe =
frais normaux de transfert × pourcentage / 100
```

Elle ne doit pas être calculée sur :

* le montant envoyé ;
* les frais de retrait ;
* le total débité.

---

# Phase 6 — Controllers

## 6.1 Modifier `TransactionController.php`

### Méthode `transfer()`

* [ ] Charger les préfixes et opérateurs disponibles.
* [ ] Afficher le formulaire d’envoi multiple.
* [ ] Afficher le pourcentage ou une explication des frais externes.
* [ ] Afficher la case d’inclusion des frais de retrait.
* [ ] Afficher les messages d’erreur et de succès.

### Méthode `doTransfer()`

* [ ] Lire la liste des numéros.
* [ ] Lire le montant total.
* [ ] Lire la case `include_withdrawal_fee`.
* [ ] Valider les données.
* [ ] Appeler `TransferService`.
* [ ] Ne pas mettre toute la logique métier dans le contrôleur.
* [ ] Retourner les erreurs précises.
* [ ] Rediriger vers l’historique après réussite.

### Historique

* [ ] Afficher les transferts multiples.
* [ ] Afficher la référence du groupe.
* [ ] Afficher le destinataire.
* [ ] Afficher l’opérateur.
* [ ] Afficher la part envoyée.
* [ ] Afficher les frais normaux.
* [ ] Afficher la commission externe.
* [ ] Afficher les frais de retrait inclus.
* [ ] Afficher le total débité pour la ligne.

---

## 6.2 Modifier `OperatorController.php`

### Gestion des opérateurs

* [ ] Créer `operators()`.
* [ ] Lister notre opérateur et les opérateurs externes.
* [ ] Ajouter un opérateur externe.
* [ ] Modifier le nom d’un opérateur externe.
* [ ] Modifier son pourcentage de commission.
* [ ] Supprimer un opérateur externe si aucune transaction ne le référence.
* [ ] Empêcher la suppression de notre opérateur.

### Gestion des préfixes

* [ ] Afficher les préfixes regroupés par opérateur.
* [ ] Ajouter un préfixe à un opérateur précis.
* [ ] Supprimer un préfixe externe.
* [ ] Vérifier l’unicité.
* [ ] Vérifier le format à trois chiffres.
* [ ] Empêcher la suppression accidentelle de tous les préfixes internes.

### Statistiques

* [ ] Calculer les frais des transferts internes.
* [ ] Calculer les frais normaux des transferts externes.
* [ ] Calculer les commissions dues aux opérateurs externes.
* [ ] Calculer les montants transférés par opérateur.
* [ ] Calculer le total à régler par opérateur.
* [ ] Séparer clairement gains, commissions et montants transférés.

---

# Phase 7 — Views

## 7.1 Créer `operator/operators.php`

* [ ] Afficher la liste des opérateurs.
* [ ] Afficher leur type : interne ou externe.
* [ ] Afficher leur pourcentage de commission.
* [ ] Ajouter un formulaire de création.
* [ ] Ajouter un bouton de modification.
* [ ] Ajouter un bouton de suppression.
* [ ] Demander une confirmation avant suppression.

---

## 7.2 Modifier `operator/prefixes.php`

* [ ] Afficher les préfixes groupés par opérateur.
* [ ] Ajouter un choix de l’opérateur.
* [ ] Ajouter un champ préfixe.
* [ ] Afficher le nom de l’opérateur associé.
* [ ] Ajouter un bouton de suppression.
* [ ] Séparer visuellement les préfixes internes et externes.

---

## 7.3 Modifier `operator/statistics.php`

Afficher au minimum quatre blocs.

### Bloc 1 — Gains internes

* [ ] Frais de retraits.
* [ ] Frais de transferts internes.

### Bloc 2 — Transferts externes

* [ ] Frais normaux collectés.
* [ ] Nombre de transferts externes.

### Bloc 3 — Commissions externes

* [ ] Total des commissions dues.
* [ ] Commission séparée par opérateur.

### Bloc 4 — Situation inter-opérateurs

Tableau :

| Opérateur | Montants transférés | Commissions | Total à régler |
| --------- | ------------------: | ----------: | -------------: |

* [ ] Ne pas appeler les montants transférés « gains ».
* [ ] Formater tous les montants en ariary.

---

## 7.4 Modifier `client/transfer.php`

* [ ] Remplacer le champ unique par une `textarea`.
* [ ] Autoriser virgules, espaces et retours à la ligne.
* [ ] Ajouter un champ de montant total.
* [ ] Afficher que le montant sera partagé équitablement.
* [ ] Ajouter la case « Inclure les frais de retrait ».
* [ ] Ajouter une explication courte.
* [ ] Ajouter une zone de prévisualisation facultative.
* [ ] Afficher le nombre de destinataires détectés.
* [ ] Afficher le montant approximatif par destinataire.
* [ ] Afficher un avertissement pour les transferts externes.

Exemple de case :

```html
<label>
    <input
        type="checkbox"
        name="include_withdrawal_fee"
        value="1"
    >

    Inclure les frais de retrait pour chaque destinataire
</label>
```

---

## 7.5 Modifier `client/history.php`

* [ ] Afficher le numéro destinataire.
* [ ] Afficher le nom de l’opérateur.
* [ ] Afficher « Interne » ou « Externe ».
* [ ] Afficher le montant.
* [ ] Afficher les frais de transfert.
* [ ] Afficher la commission.
* [ ] Afficher les frais de retrait inclus.
* [ ] Afficher la référence du transfert multiple.
* [ ] Afficher le statut.

---

# Phase 8 — Routes CodeIgniter 4

## Routes opérateur

* [ ] Ajouter la liste des opérateurs.
* [ ] Ajouter la création d’un opérateur.
* [ ] Ajouter la modification.
* [ ] Ajouter la suppression.
* [ ] Ajouter la gestion des préfixes.
* [ ] Ajouter la page statistiques.

```php
$routes->group('admin', ['filter' => 'operator'], static function ($routes) {
    $routes->get('operators', 'OperatorController::operators');
    $routes->post('operators/add', 'OperatorController::addOperator');
    $routes->post(
        'operators/update/(:num)',
        'OperatorController::updateOperator/$1'
    );
    $routes->post(
        'operators/delete/(:num)',
        'OperatorController::deleteOperator/$1'
    );

    $routes->get('prefixes', 'OperatorController::prefixes');
    $routes->post('prefixes/add', 'OperatorController::addPrefix');
    $routes->post(
        'prefixes/delete/(:num)',
        'OperatorController::deletePrefix/$1'
    );

    $routes->get('statistics', 'OperatorController::statistics');
});
```

## Routes client

```php
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('transfer', 'TransactionController::transfer');
    $routes->post('transfer', 'TransactionController::doTransfer');
    $routes->get('history', 'TransactionController::history');
});
```

---

# Phase 9 — Validation et sécurité

* [ ] Refuser une liste vide.
* [ ] Refuser un montant nul ou négatif.
* [ ] Refuser un numéro au format invalide.
* [ ] Refuser un préfixe inconnu.
* [ ] Refuser les doublons.
* [ ] Refuser l’expéditeur comme destinataire.
* [ ] Vérifier l’existence des clients internes.
* [ ] Ne pas exiger l’existence des clients externes.
* [ ] Refuser un montant individuel inférieur au minimum autorisé.
* [ ] Refuser un montant supérieur au maximum du barème.
* [ ] Vérifier le solde global avant tout débit.
* [ ] Utiliser une transaction SQL.
* [ ] Utiliser uniquement des routes POST pour les suppressions.
* [ ] Protéger les formulaires avec le CSRF de CodeIgniter.
* [ ] Valider le pourcentage de commission côté serveur.
* [ ] Échapper les données dans les vues avec `esc()`.
* [ ] Ne jamais faire confiance aux montants calculés en JavaScript.

---

# Phase 10 — Transaction SQL

* [ ] Commencer une transaction avant les débits et crédits.
* [ ] Effectuer toutes les validations avant la première écriture.
* [ ] Débiter l’expéditeur.
* [ ] Créditer les destinataires internes.
* [ ] Enregistrer les transactions internes.
* [ ] Enregistrer les transactions externes.
* [ ] Valider uniquement lorsque tout a réussi.
* [ ] Effectuer un rollback en cas d’erreur.

Exemple CodeIgniter 4 :

```php
$db = db_connect();
$db->transBegin();

try {
    // Débit de l’expéditeur
    // Crédits internes
    // Enregistrement de chaque transfert

    if ($db->transStatus() === false) {
        throw new RuntimeException(
            'Erreur pendant l’enregistrement du transfert.'
        );
    }

    $db->transCommit();
} catch (Throwable $exception) {
    $db->transRollback();
    throw $exception;
}
```

---

# Phase 11 — Tests

## Opérateurs et préfixes

* [ ] Ajouter un opérateur externe.
* [ ] Ajouter le préfixe `032`.
* [ ] Ajouter plusieurs préfixes au même opérateur.
* [ ] Refuser un préfixe déjà utilisé.
* [ ] Modifier la commission de `2 %` à `3 %`.
* [ ] Vérifier que les anciens transferts conservent leur commission enregistrée.
* [ ] Vérifier qu’un nouveau transfert utilise le nouveau taux.

## Commission

Avec :

```text
Frais normaux : 800 Ar
Pourcentage : 2 %
```

Vérifier :

```text
Commission : 16 Ar
Total des frais : 816 Ar
```

* [ ] Tester un transfert interne : commission `0`.
* [ ] Tester un transfert externe à `2 %`.
* [ ] Tester un taux avec décimales.
* [ ] Tester un taux `0 %`.
* [ ] Tester un taux négatif : refusé.
* [ ] Vérifier l’arrondi à l’entier.

## Transfert interne

* [ ] Destinataire interne existant.
* [ ] Destinataire interne inexistant.
* [ ] Solde suffisant.
* [ ] Solde insuffisant.
* [ ] Vérification du crédit du destinataire.

## Transfert externe

* [ ] Numéro externe valide.
* [ ] Numéro externe absent de `users`.
* [ ] Vérifier qu’aucun faux client n’est créé.
* [ ] Vérifier le montant à régler à l’opérateur.
* [ ] Vérifier la commission enregistrée.

## Envoi multiple

* [ ] Deux destinataires internes.
* [ ] Deux destinataires externes.
* [ ] Mélange interne et externe.
* [ ] Numéros séparés par virgules.
* [ ] Numéros séparés par espaces.
* [ ] Numéros séparés par retours à la ligne.
* [ ] Suppression des doublons.
* [ ] Refus de l’expéditeur comme destinataire.
* [ ] Test de `1 000 Ar` partagé entre trois personnes.
* [ ] Vérifier que la somme distribuée vaut exactement `1 000 Ar`.
* [ ] Vérifier que tout est annulé si un destinataire est invalide.

## Frais de retrait inclus

* [ ] Case non cochée.
* [ ] Case cochée.
* [ ] Calcul des frais pour chaque destinataire.
* [ ] Transfert interne avec inclusion.
* [ ] Transfert externe avec inclusion.
* [ ] Envoi multiple avec inclusion.
* [ ] Vérifier que la commission n’est pas calculée sur les frais de retrait.

## Statistiques

* [ ] Gains internes corrects.
* [ ] Frais normaux externes corrects.
* [ ] Commissions externes correctes.
* [ ] Montants regroupés par opérateur.
* [ ] Plusieurs préfixes correctement réunis sous le même opérateur.
* [ ] Total à régler correctement calculé.

---

# Phase 12 — Mise à jour des fichiers obligatoires

## `base.sql`

* [ ] Ajouter toutes les nouvelles tables et colonnes.
* [ ] Insérer les données initiales.
* [ ] Vérifier que le script fonctionne sur une base vide.
* [ ] Vérifier qu’il peut être exécuté sans intervention manuelle inutile.

## `Taches.md`

* [ ] Garder les tâches de la Version 1.
* [ ] Ajouter une section Version 2.
* [ ] Indiquer les membres responsables de chaque fonctionnalité.
* [ ] Indiquer les fichiers modifiés par chacun.
* [ ] Ne pas supprimer l’historique de la V1.

## `README.md`

* [ ] Décrire les fonctionnalités de la Version 2.
* [ ] Expliquer le calcul de la commission.
* [ ] Expliquer l’envoi multiple.
* [ ] Expliquer l’option d’inclusion des frais de retrait.
* [ ] Ajouter les instructions pour recréer la base.

---

# Phase 13 — Livraison avec le tag `v2`

* [ ] Tester le projet depuis une base vide.
* [ ] Vérifier les erreurs PHP.
* [ ] Vérifier les logs dans `writable/logs`.
* [ ] Vérifier toutes les routes.
* [ ] Vérifier l’interface client.
* [ ] Vérifier l’interface opérateur.
* [ ] Mettre à jour `Taches.md`.
* [ ] Mettre à jour `README.md`.
* [ ] Vérifier que le fichier `.db` n’est pas versionné.

Commandes :

```bash
git status
git add .
git commit -m "Version 2 : transferts externes et envois multiples"
git tag -a v2 -m "Version 2 Mobile Money"
git push origin main
git push origin v2
```

Vérification :

```bash
git tag
git show v2
```

# Résumé définitif du calcul de la commission

La règle à programmer est exactement :

```text
Commission externe =
frais normaux de transfert × pourcentage configuré / 100
```

Puis :

```text
Total des frais =
frais normaux de transfert + commission externe
```

Et sans frais de retrait inclus :

```text
Total débité =
montant envoyé + total des frais
```

Avec frais de retrait inclus :

```text
Total débité =
montant demandé
+ frais de retrait
+ frais normaux de transfert
+ commission externe
```

Exemple final :

```text
Montant demandé                 : 100 000 Ar
Frais de transfert              :     800 Ar
Commission externe à 2 %        :      16 Ar
Frais de retrait inclus         :     800 Ar
Montant transmis au destinataire: 100 800 Ar
Total débité                    : 101 616 Ar
```

C’est cette règle qui doit être utilisée dans le service, stockée séparément dans `transactions` et affichée dans les statistiques opérateur.


git config user.name "Maximusia"
git config user.email "tsarandromaximusia@gmail.com"

git config user.name "Eddy"
git config user.email "eddyprosper199@gmail.com"