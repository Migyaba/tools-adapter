# Tools Adapter

Extension WordPress / Elementor pour boutiques **WooCommerce** : widgets catalogue, filtres AJAX et archive produits.

**Version :** 1.4.0  
**Auteur :** [Miguel Missetcho](https://miguelmissetcho.com/)

## Prérequis

- WordPress 5.9+
- PHP 7.4+
- [Elementor](https://wordpress.org/plugins/elementor/) (testé jusqu'à 3.25)
- [WooCommerce](https://wordpress.org/plugins/woocommerce/)

## Installation

1. Téléchargez ou clonez ce dépôt dans `wp-content/plugins/tools-adapter/`
2. Activez **Tools Adapter** dans **Extensions**
3. Vérifiez qu’Elementor et WooCommerce sont actifs
4. Dans Elementor, ouvrez la catégorie **Tools Adapter** pour ajouter les widgets

## Widgets inclus

| Widget | Description |
|--------|-------------|
| **Archive Produits** | Liste filtrable avec pagination AJAX (catégories, tri) |
| **Filtre Prix** | Slider / plage de prix avec filtrage sans rechargement |
| **Catégories Produits** | Navigation par catégories WooCommerce |
| **Grille Produits** | Grille responsive (colonnes, espacement, requête produits) |
| **Carrousel Produits** | Carrousel de produits avec contrôles Elementor |

## Fonctionnalités

- Filtrage AJAX (archive + prix) avec nonces WordPress
- Cartes produit réutilisables (`Product_Card`)
- Requêtes WooCommerce centralisées (`Products_Query`)
- Catégories Produits : disposition « Carte » ou « Cercle », avec repli automatique sur une image produit si la catégorie n'a pas d'image
- Traductions front : français (source), anglais (`en_US`), polonais (`pl_PL`)
- Styles et scripts chargés à la demande par widget

## Historique des versions

### 1.4.0
- Widget **Catégories Produits** : nouveau contrôle « Disposition image » avec styles **Carte** (existant) et **Cercle** (avatar centré, image + nom + compteur)
- Repli automatique sur l'image d'un produit de la catégorie quand aucune image de catégorie n'est définie (ordre : plus récent ou aléatoire)
- Nouveaux réglages de style dédiés au cercle (taille, espacement, fond, bordure, ombre)
- Toggle « Afficher le nom » indépendant du compteur, pour un affichage 100 % personnalisable

### 1.3.4
- Version initiale publiée

## Structure

```
tools-adapter/
├── tools-adapter.php          # Point d'entrée du plugin
├── includes/
│   ├── plugin.php             # Bootstrap Elementor
│   ├── ajax-archive.php       # AJAX archive
│   ├── ajax-price-filter.php  # AJAX filtre prix
│   ├── products-query.php     # Requêtes WooCommerce
│   ├── product-card.php       # Rendu carte produit
│   ├── i18n.php               # Traductions front
│   └── widgets/               # Widgets Elementor
├── assets/css/                # Feuilles de style
├── assets/js/                 # Scripts front
└── languages/                 # Fichiers de traduction PHP
```

## Développement

Aucune étape de build requise : PHP, CSS et JS natifs.

Pour tester en local :

```bash
# Copier le plugin dans votre installation WordPress
cp -r tools-adapter /chemin/vers/wp-content/plugins/
```

## Licence

GPL-2.0-or-later — voir [LICENSE](LICENSE).

## Contribution

Issues et pull requests bienvenues sur GitHub.
