# Tools Adapter

Extension WordPress / Elementor pour boutiques **WooCommerce** : widgets catalogue, filtres AJAX et archive produits.

**Version :** 1.7.0  
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
| **Archive Produits** | Liste filtrable avec pagination AJAX (catégories, tri) — 4 types de pagination personnalisables |
| **Filtre Prix** | Slider / plage de prix avec filtrage sans rechargement |
| **Catégories Produits** | Navigation par catégories WooCommerce |
| **Grille Produits** | Grille responsive (colonnes, espacement, requête produits) |
| **Carrousel Produits** | Carrousel de produits avec contrôles Elementor |
| **Hero / Bannière** | Section d'accroche pleine largeur (titre, description, 2 boutons, fond image/couleur/dégradé) |
| **Bande CTA** | Bloc pleine largeur titre + description + bouton d'appel à l'action |
| **Compteurs / Statistiques** | Chiffres animés au scroll (repeater, icônes, préfixe/suffixe) |
| **Logos partenaires** | Grille statique ou défilement continu (marquee) des logos clients/partenaires |
| **FAQ Accordéon** | Questions/réponses repliables avec balisage SEO schema.org FAQPage |
| **Bloc réassurance** | Icônes + texte (livraison, paiement sécurisé, retours...) |
| **Bannière catégorie** | Hero pour une catégorie WooCommerce (image avec repli produit, description, compteur, CTA) |
| **Témoignages** | Avis clients en grille ou carrousel (photo, note, citation) |
| **Équipe** | Grille de membres avec photo, poste, bio et réseaux sociaux |
| **Tableau de tarifs** | Carte de plan (prix, fonctionnalités incluses/exclues, bouton, ruban « populaire ») |
| **Timeline** | Frise chronologique verticale, alternée ou en colonne unique |
| **Avant / Après** | Slider comparatif de deux images (glisser à la souris ou au doigt) |
| **Table des matières** | Sommaire auto-généré à partir des titres de la page, avec surlignage de la section active |
| **Barre de progression** | Barre fixe indiquant l'avancement de lecture de la page |

## Fonctionnalités

- Filtrage AJAX (archive + prix) avec nonces WordPress
- Cartes produit réutilisables (`Product_Card`)
- Requêtes WooCommerce centralisées (`Products_Query`)
- Catégories Produits : disposition « Carte » ou « Cercle », avec repli automatique sur une image produit si la catégorie n'a pas d'image
- Archive Produits : 4 types de pagination (numérotée classique, précédent/suivant, « charger plus », défilement infini), entièrement personnalisables et synchronisés entre le premier affichage et l'AJAX
- 7 widgets de mise en page générale (Phase 1) : Hero, Bande CTA, Compteurs animés, Logos partenaires, FAQ Accordéon, Bloc réassurance, Bannière catégorie — tous avec contrôles de contenu et de style complets (couleurs, typographie, espacement, bordures, ombres, responsive)
- 7 widgets de contenu & preuve sociale (Phase 2) : Témoignages, Équipe, Tableau de tarifs, Timeline, Avant/Après, Table des matières, Barre de progression — même niveau de personnalisation complète
- Traductions front : français (source), anglais (`en_US`), polonais (`pl_PL`)
- Styles et scripts chargés à la demande par widget

## Historique des versions

### 1.7.0
- 7 nouveaux widgets « contenu & preuve sociale » (Phase 2) :
  - **Témoignages** : repeater avis clients (photo, nom, fonction, note, citation), disposition grille ou carrousel (flèches, puces, lecture automatique)
  - **Équipe** : repeater membres (photo, nom, poste, bio, réseaux sociaux Facebook/X/Instagram/LinkedIn), photo ronde ou carrée
  - **Tableau de tarifs** : une carte de plan par widget (nom, prix, période, liste de fonctionnalités incluses/exclues, bouton, ruban « populaire », style mis en avant)
  - **Timeline** : frise chronologique verticale alternée ou en colonne unique, icônes personnalisables par étape
  - **Avant / Après** : slider comparatif de deux images, orientation horizontale/verticale, position initiale réglable, libellés personnalisables
  - **Table des matières** : sommaire généré automatiquement à partir des titres H2/H3/H4 de la page, numérotation, repli, surlignage de la section active au scroll
  - **Barre de progression de lecture** : barre fixe (haut ou bas) suivant l'avancement de lecture de toute la page ou d'une zone ciblée
- Nouveau script partagé `simple-carousel.js` (carrousel léger réutilisable, flèches/puces/autoplay/swipe tactile)
- Chaque widget dispose d'un onglet Style complet (couleurs, typographie, espacement responsive, bordures, ombres) pour une personnalisation totale

### 1.6.0
- 7 nouveaux widgets de mise en page générale, indépendants de WooCommerce (sauf Bannière catégorie), pour construire des pages professionnelles complètes :
  - **Hero / Bannière** : titre, sur-titre, description, 2 boutons, fond image/couleur/dégradé, superposition
  - **Bande CTA** : disposition horizontale ou empilée, icône de bouton
  - **Compteurs / Statistiques** : animation au scroll, décimales, préfixe/suffixe, icônes
  - **Logos partenaires** : mode grille ou défilement continu, niveaux de gris au repos
  - **FAQ Accordéon** : ouverture multiple ou simple, données structurées SEO (FAQPage)
  - **Bloc réassurance** : badges icône + titre + description, disposition ligne/colonne, séparateurs
  - **Bannière catégorie** : réutilise la logique de repli image des Catégories Produits (nouveau helper partagé `Products_Query::get_category_image_id()`)
- Chaque widget dispose d'un onglet Style complet (couleurs, typographie, espacement responsive, bordures, ombres) pour une personnalisation totale

### 1.5.0
- Widget **Archive Produits** : refonte complète de la pagination avec 4 types au choix
  - **Numérotée (classique)** : numéros avec points de suspension pour les grandes séries, flèches Précédent/Suivant optionnelles
  - **Précédent / Suivant** : deux boutons + indicateur « page X / Y »
  - **Bouton « Charger plus »** : ajoute les produits suivants à la grille sans recharger la page, avec texte de progression personnalisable
  - **Défilement infini** : charge automatiquement la page suivante quand l'utilisateur approche du bas de la grille (distance de déclenchement réglable)
- Nouvelle classe partagée `Pagination` : rendu HTML unique garantissant une cohérence parfaite entre le premier affichage et les réponses AJAX
- Nouveaux contrôles de style dédiés à la pagination (couleurs, arrondi, espacement, opacité désactivé, style du bouton « Charger plus », couleur du loader infini)

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
│   ├── pagination.php         # Rendu pagination Archive (partagé PHP/AJAX)
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
