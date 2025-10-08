# Kids-Luxe Pop-Up Upsell by Codboost

Plugin WooCommerce premium affichant un pop-up d’upsell Kids-Luxe au moment clé de l’ajout au panier ou de l’achat immédiat. Pensé pour la performance, l’UX et la sécurité.

## Fonctionnalités principales

- **Upsell ciblé par produit** : activer/désactiver le pop-up, choisir le produit complémentaire, définir la marge (montant fixe ou pourcentage) et choisir les déclencheurs (`add_to_cart`, `buy_now`).
- **Prix ajusté au panier uniquement** : le profit est appliqué via `woocommerce_before_calculate_totals` sans toucher au prix catalogue.
- **Pop-up premium accessible** : modal responsive, focus trap, fermeture clavier/overlay, design Kids-Luxe (fond sombre, accent jaune).
- **AJAX sécurisé** : ajout du produit complémentaire avec nonce, sanitization et rafraîchissement du mini-panier.
- **Personnalisation globale** : page de réglages (activation, textes, sélecteur « Acheter maintenant », affichage de la marge) + filtres & template surchargeable.

## Arborescence

```
kidsluxe-popup-upsell/
├─ kidsluxe-popup-upsell.php
├─ includes/
│  ├─ class-KLPU-Admin.php
│  ├─ class-KLPU-Frontend.php
│  ├─ class-KLPU-Ajax.php
│  ├─ class-KLPU-Price.php
│  └─ helpers.php
├─ assets/
│  ├─ css/popup.css
│  └─ js/popup.js
├─ templates/modal-offer.php
├─ languages/kidsluxe-popup-upsell.pot
├─ README.txt
└─ uninstall.php
```

## Hooks & filtres

- `klpu_buy_now_selector` : personnalise le sélecteur du bouton « Acheter maintenant ».
- `klpu_should_show_for_product` : autorise/empêche le pop-up sur un produit.
- `klpu_should_open_on_add_to_cart` : contrôle l’ouverture selon le contexte (`add_to_cart`, `buy_now`).
- `klpu_offer_title`, `klpu_offer_subtitle`, `klpu_offer_price_label` : modifie les textes affichés.

## Développement

- Code compatible PHP ≥ 8.1, WordPress ≥ 6.4, WooCommerce ≥ 8.x.
- JavaScript vanilla, CSS scoped dans `assets/css/popup.css`.
- Sécurité : vérification des capacités en admin, nonces, sanitization/escaping systématiques.
- I18N : text-domain `kidsluxe-popup-upsell`, fichier `.pot` fourni.

## Tests

Un squelette de tests unitaires peut être ajouté pour valider le calcul de profit (`KLPU_calculate_price_with_profit`). Prévoir un bootstrap WooCommerce lors de l’intégration continue.

## Licence

GPL-2.0-or-later.
