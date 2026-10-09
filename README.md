# Tools Adapter

Extension WordPress / Elementor pour boutiques **WooCommerce** & sites professionnels : widgets catalogue, filtres AJAX, galeries dynamiques et cartographie interactive.

**Version :** 2.11.0  
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
4. Dans le menu d'administration **Tools Adapter**, choisissez les widgets à activer (tous activés par défaut)
5. Dans Elementor, ouvrez la catégorie **Tools Adapter** pour ajouter les widgets activés

## Page de réglages

Le menu d'administration **Tools Adapter** (icône de prise, dans la barre latérale de wp-admin) donne accès à une page de réglages permettant d'activer ou de désactiver individuellement chacun des 37 widgets, regroupés par catégorie (Mise en page générale, Contenu & preuve sociale, Catalogue & boutique, Découverte & filtrage, Site & navigation), ainsi que les fonctionnalités globales (Constructeur En-tête & Pied de page, Vue rapide produit, Sélecteur de variations visuel, Balises dynamiques).

- Tous les widgets sont **activés par défaut** (aucune configuration requise, rétrocompatible avec les installations existantes)
- Un widget désactivé disparaît immédiatement du panneau Elementor et n'enregistre plus aucun style ni script sur le site
- Recherche instantanée, activation/désactivation en masse (globale ou par catégorie), compteurs en temps réel
- Réglages enregistrés via l'API Settings native de WordPress (nonce, capability `manage_options`)

## Widgets inclus

| Widget | Description |
|--------|-------------|
| **Archive Produits** | Liste filtrable avec pagination AJAX (catégories, tri) — 4 types de pagination personnalisables |
| **Filtre Prix** | Slider / plage de prix avec filtrage sans rechargement |
| **Catégories Produits** | Navigation par catégories WooCommerce |
| **Grille Produits** | Grille responsive (colonnes, espacement, requête produits) |
| **Carrousel Produits** | Carrousel de produits avec contrôles Elementor |
| **Hero / Bannière** | Section d'accroche pleine largeur (titre, description, 2 boutons, fond image/couleur/dégradé) ; option carte vitrée « Split Hero » avec choix rapides (icône, sous-titre, badge, flèche) et pied téléphone |
| **Bande CTA** | Bloc pleine largeur titre + description + bouton d'appel à l'action |
| **Compteurs / Statistiques** | Chiffres animés au scroll (repeater, icônes, préfixe/suffixe) |
| **Logos partenaires** | Grille statique ou défilement continu (marquee) des logos clients/partenaires |
| **FAQ Accordéon** | Questions/réponses repliables avec balisage SEO schema.org FAQPage |
| **Bloc réassurance** | Icônes + texte (livraison, paiement sécurisé, retours...) |
| **Bannière catégorie** | Hero pour une catégorie WooCommerce (image avec repli produit, description, compteur, CTA) |
| **Boîte d'icône** | Carte moderne avec badge d'icône flottant, fond décoratif décalé et micro-animations |
| **Boîtes d'image (Grille & Carrousel)** | Cartes de services avec image, badge personnalisé, liste à puces (coche) et lien d'action, en grille responsive ou carrousel défilant |
| **Cartes services Bento** | Cartes image plein fond avec voile dégradé, badge, titre, description, pastilles et lien fléché ; largeur de chaque carte sur 12 colonnes (ex. 7 + 5 puis 12), option carte entière cliquable |
| **Carte d'orientation** | Carte vitrée « De quoi avez-vous besoin ? » : choix cliquables avec pastille d'icône, titre, sous-titre, badge optionnel et flèche, pied de carte téléphone avec icône |
| **Images superposées** | Grande image + médaillon secondaire qui la chevauche (coin, débordement, bordure) et pastille ronde chiffrée avec compteur animé (« 27 ans d'excellence »), animations flottement / halo / anneau |
| **Carte de tarifs** | Titre, description, badge (coin / au-dessus / à côté) et lignes « libellé … prix / unité » avec séparateurs, ancien prix barré, ligne mise en avant, mention et bouton ; style clair ou sombre |
| **Simulateur de Devis / Bois** | Calcul en direct essence × longueur × quantité : grille fixe ou liste libre de tarifs (longueurs indisponibles grisées), boutons ou listes déroulantes, quantité décimale, total côte à côte ou grand total centré avec détail, thème sombre / clair, pré-remplissage du formulaire de contact |
| **Galerie Projets Mosaïque** | Grille Bento 6 cadres avec diaporama en fondu (FADE) indépendant par projet |
| **Étapes / Processus** | Déroulement étape par étape avec ligne de connexion et badge Icône ou Numéro |
| **Témoignages** | Avis clients en grille ou carrousel (photo, note, citation) |
| **Équipe** | Grille de membres avec photo, poste, bio et réseaux sociaux |
| **Tableau de tarifs** | Carte de plan (prix, fonctionnalités incluses/exclues, bouton, ruban « populaire ») |
| **Timeline** | Frise chronologique verticale, alternée ou en colonne unique |
| **Avant / Après** | Slider comparatif de deux images : glisser souris/doigt (sans bloquer le défilement mobile), clavier et lecteurs d'écran, mode « suit la souris », horizontal/vertical, format (16:9…), libellés stylés séparément, icône du curseur, légende « Glissez pour comparer » |
| **Table des matières** | Sommaire auto-généré à partir des titres de la page, avec surlignage de la section active |
| **Barre de progression** | Barre fixe indiquant l'avancement de lecture de la page |
| **Mini-panier** | Icône panier + dropdown AJAX (articles, sous-total, retrait, liens panier/commande) |
| **Barre panier collante** | Barre fixe en bas de page produit (image, prix, quantité, ajout au panier) |
| **Vue rapide produit** | Bouton « œil » sur chaque carte produit ouvrant une fiche AJAX en modale |
| **Guide des tailles** | Bouton ouvrant un tableau de correspondance des tailles personnalisable |
| **Filtre par attributs** | Pastilles/pilules pour filtrer l'Archive/Grille par un attribut WooCommerce (couleur, taille…) |
| **Marques** | Grille de logos de marques (taxonomie native ou attribut personnalisé) |
| **Produits récemment consultés** | Historique client-side (localStorage) rendu en cartes produit réelles |
| **Compte à rebours promo** | Minuteur configurable (date fixe ou fin de promo du produit) |
| **Barre de stock / urgence** | Message + barre de progression selon le stock restant du produit |
| **Mega Menu** | Menu de navigation horizontal avec panneaux mega-menu, option collant au défilement, repli mobile |
| **Bandeau cookies (RGPD)** | Bannière de consentement avec catégories de cookies personnalisables, mémorisée par cookie navigateur |
| **Popup preuve sociale** | Notifications flottantes rotatives — messages personnalisés ou commandes WooCommerce récentes |
| **Formulaire de contact** | Formulaire stylisé avec envoi AJAX par e-mail, anti-spam (honeypot + limite de fréquence) |
| **Carte Google Maps** | Intégration par adresse (sans clé API) ou code d'intégration personnalisé, carte d'infos flottante |
| **Carte Interactive des Zones** | Carte dynamique Leaflet (sans clé payante) avec rayons d'intervention concentriques, marqueurs pulsants animés, filtres de communes et testeur d'éligibilité postal |
| **Grille de blog** | Grille personnalisable d'articles WordPress (catégories, colonnes, extrait, pagination) |

## Balises dynamiques

Tous les champs **lien**, **image** et **texte** des widgets acceptent les balises dynamiques d'Elementor : cliquez sur l'icône base de données (🗄) à droite d'un champ, puis choisissez une balise du groupe **Tools Adapter**. Elles fonctionnent **avec ou sans Elementor Pro** (avec Pro, ses propres balises restent disponibles dans les mêmes champs).

| Type | Balises |
|---|---|
| **Liens** | Lien de la page courante, d'une page au choix, accueil du site, téléphone (`tel:`), e-mail (`mailto:` + objet), WhatsApp (numéro + message), Google Maps (lieu ou itinéraire), partage (Facebook, X, LinkedIn, WhatsApp, e-mail), page WooCommerce (boutique, panier, commande, compte) |
| **Textes** | Titre, extrait, date de publication (format au choix), auteur, catégorie/terme, nom et slogan du site, année en cours, champ personnalisé, paramètre d'URL (`?prestation=…`), prix produit WooCommerce |
| **Images** | Image mise en avant (avec image de secours), logo du site, champ personnalisé (ID ou URL) |

À savoir :
- Chaque balise propose ses propres réglages (numéro, adresse, format…) et les options communes « Avant », « Après » et « Valeur de secours » d'Elementor.
- L'e-mail destinataire du formulaire de contact n'est volontairement **pas** dynamique (sécurité anti-relais de spam). Les champs techniques (ancres, clés, devise…) ne le sont pas non plus.
- Avec un cache de pages, excluez du cache les pages qui utilisent la balise « Paramètre d'URL ».
- Désactivable dans **Tools Adapter → Réglages → Balises dynamiques**.
- Technique : les widgets héritent de `ToolsAdapter\Base_Widget` / `ToolsAdapter\Repeater` (`includes/dynamic-support.php`), qui activent `dynamic.active` sur les contrôles URL / MEDIA / TEXT / TEXTAREA ; les balises sont dans `includes/dynamic-tags.php`.

## Fonctionnalités

- Filtrage AJAX (archive + prix) avec nonces WordPress
- Cartes produit réutilisables (`Product_Card`)
- Requêtes WooCommerce centralisées (`Products_Query`)
- Catégories Produits : disposition « Carte » ou « Cercle », avec repli automatique sur une image produit si la catégorie n'a pas d'image
- Archive Produits : 4 types de pagination (numérotée classique, précédent/suivant, « charger plus », défilement infini), entièrement personnalisables et synchronisés entre le premier affichage et l'AJAX
- 7 widgets de mise en page générale (Phase 1) : Hero, Bande CTA, Compteurs animés, Logos partenaires, FAQ Accordéon, Bloc réassurance, Bannière catégorie — tous avec contrôles de contenu et de style complets (couleurs, typographie, espacement, bordures, ombres, responsive)
- 7 widgets de contenu & preuve sociale (Phase 2) : Témoignages, Équipe, Tableau de tarifs, Timeline, Avant/Après, Table des matières, Barre de progression — même niveau de personnalisation complète
- 4 widgets de conversion boutique (Phase 3) : Mini-panier AJAX, Barre panier collante, Vue rapide produit, Guide des tailles
- Bouton « Vue rapide » optionnel sur les cartes produit (Grille, Carrousel, Archive), ouvrant une fiche produit complète (galerie, prix, formulaire d'ajout au panier réel) sans quitter la page
- Sélecteur de variations visuel : remplace automatiquement les listes déroulantes WooCommerce par des pastilles de couleur ou pilules de texte sur toutes les pages produit variable, en conservant la compatibilité totale avec le script de variations natif
- 5 widgets de découverte & filtrage (Phase 4) : Filtre par attributs, Marques, Produits récemment consultés, Compte à rebours promo, Barre de stock/urgence
- L'Archive/Grille Produits peut désormais être filtrée par n'importe quel attribut WooCommerce global (couleur, taille…), en plus des catégories et du prix, via le même mécanisme AJAX partagé
- 6 widgets « site-wide & navigation » (Phase 5) : Mega Menu, Bandeau cookies (RGPD), Popup preuve sociale, Formulaire de contact, Carte Google Maps, Grille de blog — complétant l'ensemble des 5 phases prévues
- Nouvel endpoint AJAX `Ajax_Contact`, protégé par nonce et par une limite de fréquence anti-spam (5 envois / minute / IP)
- Traductions front : français (source), anglais (`en_US`), polonais (`pl_PL`)
- Styles et scripts chargés à la demande par widget

### 2.2.0
- **Refonte majeure du widget Témoignages (Carrousel d'avis clients)** :
  - Support du **carrousel multi-cartes** : 3 avis visibles par écran sur Ordinateur, 2 sur Tablette, 1 sur Mobile (entièrement configurable de 1 à 6).
  - Nouvelle structure de carte moderne : 5 étoiles d'évaluation dorées en haut, texte du témoignage en italique avec guillemets français (« ») au centre, méta de l'auteur en bas (avatar, nom en gras et fonction/lieu).
  - 3 styles d'indicateurs de pagination (Dots) : **Anneau actif moderne (Ring)** avec double cercle, **Pastille (Pill)** ou **Point classique (Bullet)**.
  - Personnalisation poussée dans Elementor : typographie, couleurs, taille et écartement des étoiles, forme et bordure de l'avatar (cercle, coins arrondis, carré), fond, bordure et effet d'élévation au survol de chaque carte.
  - Moteur `simple-carousel.js` étendu pour le défilement responsive multi-colonnes, pagination dynamique, glissement tactile (touch swipe) et arrêt au survol.
- Nouveau widget **Étapes / Processus** : déroulement horizontal (ou vertical sur mobile) avec ligne de connexion continue et badges personnalisables (Icônes vectorielles ou Numérotation automatique / personnalisée).
- Nouveau widget **Galerie Projets Mosaïque** : grille Bento 6 cadres (projets/réalisations) avec diaporama d'images en fondu (FADE) indépendant, désynchronisation automatique, pause au survol et dégradé protecteur.
- Nouveau widget **Boîte d'icône** : carte de service/avantage avec badge d'icône flottant débordant, fond décoratif décalé (*offset backdrop*), sur-titre, titre, description, actions de lien et micro-animations au survol.

### 2.1.0
- Nouvelle **page de réglages** dans l'administration WordPress (menu « Tools Adapter ») :
  - Active/désactive individuellement chacun des 36 widgets Elementor, regroupés par catégorie (Mise en page générale, Contenu & preuve sociale, Catalogue & boutique, Découverte & filtrage, Site & navigation)
  - 2 fonctionnalités globales également commutables : Vue rapide produit, Sélecteur de variations visuel
  - Recherche instantanée, activation/désactivation en masse (globale ou par catégorie), compteurs en temps réel, avertissement de modifications non enregistrées
  - Un widget désactivé disparaît du panneau Elementor et n'enregistre plus aucun style/script — aucun impact sur les performances des sites qui n'utilisent pas certains widgets
  - Tout est activé par défaut : rétrocompatible avec les installations existantes, aucune action requise après la mise à jour
  - Réglages persistés via l'API Settings native de WordPress (`register_setting`, nonce, capability `manage_options`), un lien « Réglages » est aussi ajouté sur la page des extensions

### 2.0.0
- 6 nouveaux widgets « site-wide & navigation » (Phase 5), qui complètent l'ensemble des 5 phases de la feuille de route :
  - **Mega Menu** : rendu à partir de n'importe quel menu WordPress (Apparence → Menus), panneaux mega-menu multi-colonnes pour les éléments ayant des sous-menus, option collante au défilement, repli mobile avec bouton burger, breakpoint personnalisable
  - **Bandeau cookies (RGPD)** : message personnalisable, boutons Accepter / Refuser / Personnaliser, catégories de cookies configurables (repeater, catégories verrouillables), mémorisation via cookie navigateur (nom et durée personnalisables), disposition barre pleine largeur ou boîte flottante, position haut/bas, événement JS `tools-adapter:cookie-consent` émis pour s'intégrer à des scripts tiers (Analytics, pixels…)
  - **Popup preuve sociale** : notifications flottantes rotatives dans un coin de l'écran — messages entièrement personnalisés (repeater) ou générés automatiquement à partir des commandes WooCommerce récentes (produit + ville + délai relatif), délais et position configurables
  - **Formulaire de contact** : champs nom/e-mail/téléphone/sujet/message personnalisables, envoi AJAX par e-mail (`wp_mail`, en-tête Reply-To automatique), protection anti-spam (champ honeypot invisible + limite de 5 envois/minute/IP côté serveur), messages de succès/erreur personnalisables
  - **Carte Google Maps** : intégration par simple adresse (aucune clé API requise) ou via un code d'intégration Google Maps personnalisé, effet noir & blanc au repos, carte d'informations flottante (titre, adresse, horaires, bouton itinéraire) positionnable dans les 4 coins
  - **Grille de blog** : grille d'articles WordPress natifs (catégories, tri, colonnes responsives), image mise en avant, badge catégorie, date/auteur, extrait de longueur réglable, lien « Lire la suite », pagination native WordPress optionnelle
- Nouvel endpoint AJAX `Ajax_Contact`, protégé par nonce et par une limite de fréquence anti-spam
- L'ensemble des 5 phases de la feuille de route « Tools Adapter » est désormais implémenté : 33 widgets Elementor au total, tous personnalisables de façon professionnelle (contenu et style)

### 1.9.0
- 5 nouveaux widgets « découverte & filtrage » (Phase 4) :
  - **Filtre par attributs** : expose n'importe quel attribut WooCommerce global (couleur, taille…) en pastilles ou pilules cliquables, synchronisé avec l'Archive/Grille Produits via l'AJAX partagé (comme le filtre de catégories existant)
  - **Marques** : grille de logos cliquables (taxonomie native `product_brand` si disponible, sinon tout attribut utilisé comme marque), niveaux de gris au repos, repli sur le nom si pas de logo
  - **Produits récemment consultés** : suivi 100% client-side (localStorage, aucune donnée serveur), hydraté en vraies cartes produit via un nouvel endpoint AJAX dédié
  - **Compte à rebours promo** : minuteur configurable — date fixe ou fin de promo automatique du produit courant (`_sale_price_dates_to`), libellés et textes entièrement personnalisables
  - **Barre de stock / urgence** : message + barre de progression n'apparaissant que sous un seuil de stock configurable, pour créer un sentiment d'urgence sur les pages produit
- `Ajax_Archive` étendu pour accepter des filtres par attributs (`attribute_filters`) en plus des catégories et du prix, avec la même cohérence premier-affichage/AJAX que le reste de l'archive
- Nouveau helper partagé `Products_Query::get_attribute_taxonomy_options()` (liste des attributs WooCommerce globaux disponibles)
- Nouvel endpoint AJAX `Ajax_Recently_Viewed`, protégé par nonce

### 1.8.0
- 4 nouveaux widgets « conversion boutique » (Phase 3) :
  - **Mini-panier** : icône + compteur, dropdown AJAX (miniatures, quantité, prix, retrait d'un article), sous-total et liens panier/commande, se rafraîchit automatiquement après tout ajout au panier sur la page
  - **Barre panier collante** : apparaît en bas d'écran dès que le formulaire d'achat du produit sort du viewport ; quantité + ajout direct pour les produits simples, bouton de défilement vers les options pour les produits variables
  - **Vue rapide produit** : bouton « œil » désormais disponible sur les cartes de la Grille, du Carrousel et de l'Archive Produits (nouveau réglage « Bouton vue rapide »), ouvre une fiche AJAX complète (galerie, prix, description courte, formulaire d'ajout au panier réel WooCommerce) dans une fenêtre modale
  - **Guide des tailles** : bouton personnalisable ouvrant un tableau de correspondance des tailles entièrement paramétrable (lignes, unité cm/pouces, notes complémentaires en WYSIWYG)
- Nouvelle infrastructure modale partagée (`modal.js` / `modal.css`), réutilisée par la Vue rapide et le Guide des tailles
- Nouveau **Sélecteur de variations visuel** : remplace automatiquement les listes déroulantes d'attributs WooCommerce (couleur, taille…) par des pastilles/pilules cliquables sur toute page produit variable, tout en conservant le `<select>` natif masqué pour une compatibilité totale avec le script de variations WooCommerce (disponibilité des combinaisons, etc.)
- Nouveaux endpoints AJAX dédiés : `Ajax_Cart` (lecture/retrait du panier) et `Ajax_Quick_View` (fiche produit AJAX), tous deux protégés par nonce

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
│   ├── dynamic-support.php    # Classes de base (balises dynamiques sur les champs)
│   ├── dynamic-tags.php       # Balises dynamiques Tools Adapter (liens, textes, images)
│   ├── ajax-archive.php       # AJAX archive
│   ├── ajax-cart.php          # AJAX mini-panier (lecture/retrait)
│   ├── ajax-quick-view.php    # AJAX vue rapide produit
│   ├── ajax-recently-viewed.php # AJAX produits récemment consultés
│   ├── ajax-contact.php       # AJAX formulaire de contact
│   ├── admin-settings.php     # Page de réglages (activer/désactiver les widgets)
│   ├── variation-swatches.php # Swatches visuels pour variations WooCommerce
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
