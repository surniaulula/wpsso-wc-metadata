=== Product GTIN, UPC, EAN, ISBN, MPN and More for WooCommerce | WPSSO Add-on ===
Plugin Name: WPSSO Product Metadata for WooCommerce
Plugin Slug: wpsso-wc-metadata
Text Domain: wpsso-wc-metadata
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-wc-metadata/assets/
Tags: woocommerce, gtin, upc, ean, isbn, mpn, custom fields, global identifier, manufacturer part number, attribute, information, product
Contributors: jsmoriss
Requires PHP: 5.6
Requires At Least: 4.4
Tested Up To: 5.5.1
WC Tested Up To: 4.5.2
Stable Tag: 1.5.1

GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, Depth, and Volume for WooCommerce Products and Variations.

== Description ==

Easily include additional metadata fields in the WooCommerce "**Product data**" metabox and the purchase page "**Additional information**" section:

* GTIN
* GTIN-8
* GTIN-12 (UPC)
* GTIN-13 (EAN)
* GTIN-14
* ISBN
* MPN (Manufacturer Part Number)
* Depth
* Volume

The information shown under the WooCommerce purchase page "**Additional information**" section changes as different variations are selected (or unselected).

The *SSO &gt; WooCommerce Metadata* settings page allows you to **enable or disable product metadata with a simple checkbox**, along with customizing the label and placeholder for different languages (aka WordPress locales).

The product metadata fields are integrated seamlessly in the WooCommerce product editing page, for both simple products and product variations.

**Product global identifier values (ie. GTIN, GTIN-8, GTIN-12, GTIN-13, GTIN-14, ISBN, and MPN) are also searchable from the front-end and the WooCommerce Products admin page.**

<h3>Can You Add More Product Metadata Fields?</h3>

Absolutely. Assuming the information can be included in a [Schema Product property value](https://schema.org/Product), [create a new topic in the plugin support forum](https://wordpress.org/support/plugin/wpsso-wc-metadata/) with the details (including the suggested Schema property name) and we'll have a look. ;-)

<h3>Includes WooCommerce Fluid Volume Units</h3>

The WPSSO Product Metadata for WooCommerce add-on also includes a "**Fluid volume unit**" option in the *WooCommerce &gt; Settings &gt; Products* settings page:

* ml
* cl
* l
* kl
* US tsp
* US tbsp
* US fl oz
* US cup
* US pt
* US qt
* US gal

<h3>Includes Meta Tags and Schema Markup</h3>

The [WPSSO Core plugin](https://wpsso.com/extend/plugins/wpsso/free/) will automatically include the following Open Graph product meta tags for enabled product metadata:

* product:ean
* product:isbn
* product:mfr_part_no
* product:upc

The [Schema JSON-LD Markup add-on](https://wpsso.com/extend/plugins/wpsso-schema-json-ld/free/) will automatically include the following Schema Product and Offer properties for enabled product metadata:

* mpn
* gtin14
* gtin13
* gtin12
* gtin8
* gtin
* productID isbn
* depth
* additionalProperty fluid_volume

<h3>WPSSO Core Plugin Required</h3>

WPSSO Product Metadata for WooCommerce (aka WPSSO WCMD) is an add-on for the [WPSSO Core plugin](https://wpsso.com/extend/plugins/wpsso/free/).

Additional product information for Open Graph meta tags and Schema Product properties (like product brand, color, condition, dimensions, material, size, weight, SKU, prices and currencies, sale start / end dates, sale prices, pre-tax prices, VAT prices, etc.) requires the WooCommerce integration module provided with the <a href="https://wpsso.com/">WPSSO Core Premium plugin</a>.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Product Metadata for WooCommerce add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/install-the-plugin/).
* [Uninstall the WPSSO Product Metadata for WooCommerce add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

<h3 class="top">Frequently Asked Questions</h3>

* None.

== Screenshots ==

01. Information shown under the "Additional information" section changes as different variations are selected.
02. Enabled product metadata fields are added seamlessly under the product inventory tab.
03. Enabled depth and volume metadata fields are added seamlessly under the product shipping tab.
04. Enabled product metadata fields are added seamlessly under the product variations tab.

== Changelog ==

<h3 class="top">Version Numbering</h3>

Version components: `{major}.{minor}.{bugfix}[-{stage}.{level}]`

* {major} = Major structural code changes / re-writes or incompatible API changes.
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).

<h3>Standard Version Repositories</h3>

* [GitHub](https://surniaulula.github.io/wpsso-wc-metadata/)
* [WordPress.org](https://plugins.trac.wordpress.org/browser/wpsso-wc-metadata/)

<h3>Changelog / Release Notes</h3>

**Version 1.6.0-dev.4 (2020/10/13)**

* **New Features**
	* None.
* **Improvements**
	* Refactored the add-on class to extend a new WpssoAddOn abstract class.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Added a new WpssoAddOn class in lib/abstracts/add-on.php.
	* Added a new SucomAddOn class in lib/abstracts/com/add-on.php.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.4.
	* WPSSO Core v8.8.0-dev.4.
	* WooCommerce v3.6.4.

**Version 1.5.1 (2020/09/15)**

* **New Features**
	* None.
* **Improvements**
	* Updated the French plugin translations.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Moved extracted translation strings from lib/gettext-*.php files to a new gettext/ folder.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.4.
	* WPSSO Core v8.5.0.
	* WooCommerce v3.6.4.

== Upgrade Notice ==

= 1.6.0-dev.4 =

(2020/10/13) Refactored the add-on class to extend a new WpssoAddOn abstract class.

= 1.5.1 =

(2020/09/15) Updated the French plugin translations.

