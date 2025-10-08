=== Kids-Luxe Pop-Up Upsell by Codboost ===
Contributors: codboost
Tags: woocommerce, upsell, popup, marketing
Requires at least: 6.4
Tested up to: 6.5
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin premium et performant pour proposer un upsell contextuel Kids-Luxe lors de l’ajout au panier ou de l’achat immédiat dans WooCommerce.

== Description ==

Kids-Luxe Pop-Up Upsell by Codboost permet d’afficher un pop-up élégant et performant dès qu’un client ajoute un produit au panier ou clique sur « Acheter maintenant ». Configurez un produit complémentaire, ajoutez une marge fixe ou en pourcentage et convertissez davantage tout en respectant l’esthétique Kids-Luxe.

* Pop-up premium responsive, focus sur l’UX et l’accessibilité.
* Gestion par produit : activez l’upsell, choisissez l’offre, définissez le profit (montant ou pourcentage) et les déclencheurs.
* Ajustement du prix uniquement dans le panier grâce à un meta cart-item.
* AJAX sécurisé avec prise en charge des fragments mini-panier.
* Page de réglages pour personnaliser textes, sélecteur « Acheter maintenant » et activation globale.
* Filtrage et templates surchargés pour une intégration avancée.

== Installation ==

1. Téléchargez l’archive du plugin.
2. Téléversez le dossier `kidsluxe-popup-upsell` dans le répertoire `/wp-content/plugins/` de votre installation WordPress.
3. Activez le plugin via le menu « Extensions » dans WordPress.
4. Assurez-vous que WooCommerce est actif.

== Configuration ==

1. Ouvrez un produit dans WooCommerce et rendez-vous dans l’onglet « Pop-Up Upsell Kids-Luxe ».
2. Cochez l’activation, sélectionnez le produit à promouvoir, définissez le type et la valeur du profit et les déclencheurs.
3. Dans « Marketing → Kids-Luxe Pop-Up », personnalisez les textes, activez/désactivez globalement le pop-up et ajustez le sélecteur « Acheter maintenant » si nécessaire.

== Filters ==

* `klpu_buy_now_selector` — Personnalise le sélecteur CSS du bouton « Acheter maintenant ».
* `klpu_should_show_for_product` — Permet de conditionner l’affichage de l’upsell pour un produit donné.
* `klpu_should_open_on_add_to_cart` — Active/désactive l’ouverture du pop-up selon le contexte (`add_to_cart`, `buy_now`).
* `klpu_offer_title`, `klpu_offer_subtitle`, `klpu_offer_price_label` — Modifie les textes affichés dans le pop-up.
* Template surchargeable : `woocommerce/kidsluxe/modal-offer.php`.

== FAQ ==

= Le prix catalogue est-il modifié ? =
Non. Le plugin ajuste uniquement le prix du produit proposé dans le panier en fonction de la marge définie.

= Peut-on désactiver le pop-up sur certains produits ? =
Oui, via la case à cocher « Activer le pop-up pour ce produit ». Vous pouvez également utiliser le filtre `klpu_should_show_for_product`.

== Changelog ==

= 1.0.0 =
* Version initiale.
