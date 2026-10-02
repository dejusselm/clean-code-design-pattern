# Audit initial

## 1. Comportement observable

l'éxécution du fichier index.php insère dans la base de donnée le numéro de la réservation, le total à régler ainsi que le statut de la réservation.
On observe également qu'un mail de confirmation est envoyé au client contenant le numéro ainsi que le statut de la réservation.
Le total à régler est aussi renvoyé dans le terminal.
Il effectue aussi le paiement via le moyen indiqué

## 2. Problèmes identifiés

| # | Problème | Catégorie | Impact |
|---|---|---|---|
| 1 | Identification du moyen de paiement dans bookingService.php | Responsabilité | modification obligatoire de la classe à chaques nouveaux moyens de paiement |
| 2 | Absence de garde fous dans bookingService.php et dans StripeClient.php | Testabilité | Complication des tests à effectuer |
| 3 | "Valeurs magiques" dans bookingService.php | Lisibilité | le temps de déchiffrage et d'analyse est particulièrement long |
| 4 | Remises dans bookingService.php | Responsabilité | Modication obligatoire de la classe à chaques nouveaux types de remises |
| 5 | Code mort dans SmsClient.php, AnalyticsClient.php et LoyaltyService.php | Autre | Ajoutes des fichiers inutiles |
| 6 | Instanciation directe de StripeClient et EmailService dans BookingService.php | Couplage | Impossible d'isoler la classe et modification du code obligatoire |

## 3. Nos trois priorités

1. Le problème numéro 2, concerne plusieurs problèmes en même temps dont la testabilité, lisibilité et règles métier. De plus cela concerne plusieurs fichiers.
2. Le problème numéro 1, nous obliges à modifier la classe et entraine une perte de temps et de lisibilité.
3. Le problème numéro 4, Plusieurs problèmes sont perçus comme la responsabilité et la lisibilité en plus d'une modification obligatoire (Comme le numéro 1).

## 4. Risques avant refactoring

Les risques que pourrait engendrer le refactoring sont: une mauvaise réattribution des responsabilités, des oublis de tests, overengineering qui serait contre productif.
