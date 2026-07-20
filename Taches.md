# Taches.md

## Binome

- Etudiant 4052: Maximusia
- Etudiant 4095: Eddy

---

# Version 1 (v1)

## Etudiant 1 4052
- Creation de la base de donnees (base.sql)
- Configuration de SQLite
- Creation des Models:
  - UserModel
  - PrefixModel
  - OperationTypeModel
  - FeeRuleModel
  - TransactionModel
- Creation du helper de calcul des frais
- Tests des operations

## Etudiant 2 4095
- Configuration des routes
- Developpement des controllers
- Creation des vues (login, dashboard, depot, retrait, transfert, historique)
- Interface administrateur (gestion des prefixes et des frais)
- Integration de Bootstrap
- Validation des formulaires

---

# Version 2 (v2)

## Etudiant 1 4052 - Partie operateur

- Gestion des operateurs externes et de leurs prefixes
- Ajout et suppression des prefixes
- Configuration du pourcentage de commission
- Calcul des commissions appliquees aux frais de transfert
- Statistiques separees:
  - gains de notre operateur;
  - commissions des autres operateurs
- Affichage des montants a envoyer a chaque operateur
- Fichiers modifies:
  - `app/Controllers/OperatorController.php`
  - `app/Models/OperatorModel.php`
  - `app/Models/PrefixModel.php`
  - `app/Views/operator/operators.php`
  - `app/Views/operator/prefixes.php`
  - `app/Views/operator/statistics.php`

## Etudiant 2 4095 - Partie client et transferts

- Option "inclure les frais de retrait" lors du transfert
- Envoi multiple vers plusieurs numeros
- Division exacte du montant entre les destinataires
- Verification des numeros internes et externes
- Calcul des frais de transfert
- Ajout de la commission pour les numeros externes
- Enregistrement des transferts multiples
- Fichiers modifies:
  - `app/Controllers/TransactionController.php`
  - `app/Controllers/ClientController.php`
  - `app/Models/TransactionModel.php`
  - `app/Libraries/TransferService.php`
  - `app/Libraries/TransferFeeService.php`
  - `app/Views/client/transfer.php`
  - `app/Views/client/history.php`

## Travail commun

- Mise a jour de `base.sql`
- Mise a jour des routes
- Mise a jour de `README.md`
- Creation et publication du tag `v2`

