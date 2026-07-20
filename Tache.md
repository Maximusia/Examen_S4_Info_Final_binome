# Taches.md

## Binôme

- Étudiant 4052:  Maximusia
- Étudiant 4095 : Eddy

---

# Version 1 (v1)

## Etudiant 1 4052
- Création de la base de données (base.sql)
- Configuration de SQLite
- Création des Models :
  - UserModel
  - PrefixModel
  - OperationTypeModel
  - FeeRuleModel
  - TransactionModel
- Création du helper de calcul des frais
- Tests des opérations

## Étudiant 2 4095
- Configuration des routes
- Développement des contrôleurs
- Création des vues (login, dashboard, dépôt, retrait, transfert, historique)
- Interface administrateur (gestion des préfixes et des frais)
- Intégration de Bootstrap
- Validation des formulaires

---
## Version 2 (v2)

## Étudiant 1 4052 — Partie opérateur

* Gestion des opérateurs externes et de leurs préfixes
* Ajout et suppression des préfixes
* Configuration du pourcentage de commission
* Calcul des commissions appliquées aux frais de transfert
* Statistiques séparées :

  * gains de notre opérateur ;
  * commissions des autres opérateurs
* Affichage des montants à envoyer à chaque opérateur
* Modification de :

  * `OperatorController.php`
  * `OperatorModel.php`
  * `PrefixModel.php`
  * `operator/operators.php`
  * `operator/prefixes.php`
  * `operator/statistics.php`

## Étudiant 2 4095 — Partie client et transferts

* Option « inclure les frais de retrait » lors du transfert
* Envoi multiple vers plusieurs numéros
* Division exacte du montant entre les destinataires
* Vérification des numéros internes et externes
* Calcul des frais de transfert
* Ajout de la commission pour les numéros externes
* Enregistrement des transferts multiples
* Adaptation de :

  * `TransactionController.php`
  * `TransactionModel.php`
  * `TransferService.php`
  * `TransferFeeService.php`
  * `client/transfer.php`
  * `client/history.php`

## Travail commun

* Mise à jour de `base.sql`
* Mise à jour des routes
* Création et publication du tag `v2`

