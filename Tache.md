# Taches.md

## Binôme

- Étudiant 4052:  Maximusia
- Étudiant 4095 : Eddy

---

# Version 1 (v1)

## Etudiant 1
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

## Étudiant 2
- Configuration des routes
- Développement des contrôleurs
- Création des vues (login, dashboard, dépôt, retrait, transfert, historique)
- Interface administrateur (gestion des préfixes et des frais)
- Intégration de Bootstrap
- Validation des formulaires

---
# Livraison v2 (date)
- Étudiant 1 : 
  - Gestion des préfixes autres opérateurs (ajout/suppression)
  - Configuration du % de commission
  - Statistiques séparées (gains opérateur / autres opérateurs)
  - Montants envoyés à chaque opérateur
- Étudiant 2 :

  - Option "inclure les frais" pour le retrait
  - Envoi multiple vers plusieurs numéros (division du montant)
  - Adaptation du helper `calculate_transfer_fee` avec commission
