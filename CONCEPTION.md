# Note de conception

## 1. Choix principaux

Extraction de la logique de calcul dans BookingCalculator.php afin de ne pas surcharger le dossier BookingService.php, on a été imposer suite au règle du Ticket #102 d'isoler dans un calculator dédié.
De plus BookingService.php orcheste uniquement le processus de réservation, en déléguant les calculs de prix à BookingCalculator.php.
Les actions secondaires dans PostBookingService.php comme notifications SMS/Email, calcul des points de fidélité et persistence SQL ont été sorties du flux principal pour fluidifier la validation de la réservation.

## 2. Principes SOLID mobilisés

Pour chaque principe réellement utilisé :
problème initial : La classe BookingService accumulés trop de responsabilités avec les calculs de prix avec les remises, éxécution des technique de paiement, des envoies SMS/Email en plus des écritures logs SQL

classes concernées : BookingService.php, BookingCalculator.php, PostBookingService.php

bénéfice obtenu: Chaque classe n'a plus qu'une seule raison de changer. Modifier une règle commerciale (remise VIP) impacte uniquement BookingCalculator.php, tandis que changer le format d'un SMS n'impacte que PostBookingService.php.

## 3. Design Patterns éventuellement utilisés

Pour chaque pattern :
problème rencontré : Le kit de paiement PayFast (SDK) ne peut pas être modifié. De plus, il est difficile à utiliser car il demande les prix en centimes (un nombre entier) et sous la forme d'un tableau de données très précis, alors que notre application utilise des euros (avec des virgules). Il ne pouvait donc pas se brancher directement sur notre code.

solution retenue :  Nous avons créé une classe « traductrice » appelée PayFasteAdapter. Elle se charge de faire tout le travail invisible : elle prend le prix en euros, le multiplie par 100 pour le transformer en centimes, l'arrondit proprement et prépare le tableau de données demandé par PayFast.

pourquoi une solution plus simple ne suffisait pas.
Le code aurait été sucharger sans ce "traducteur" en préparant à la fois ces différents calculs mathématiques en plus de préparer ces tableaux


## 4. Solutions envisagées puis écartées

Aucune solution que nous avions envisagée n'a été écartée.

## 5. Ce que nous améliorerions avec plus de temps

Ajout du système d'annulation de réservation et de remboursement.
Améliorer l'architecture des fichiers.
Mieux organiser les blocs de code dans des fonctions ou méthodes.
Améliorer la lisibilité en stockant des valeurs dans des variables.