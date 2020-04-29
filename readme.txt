=== Metadata for WooCommerce | WPSSO Add-on ===
Plugin Name: WPSSO Metadata for WooCommerce
Plugin Slug: wpsso-wc-metadata
Text Domain: wpsso-wc-metadata
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-wc-metadata/assets/
Tags: woocommerce, gtin, upc, ean, mpn, isbn, metadata, attribute, information, product, custom field
Contributors: jsmoriss
Requires PHP: 5.6
Requires At Least: 4.2
Tested Up To: 5.4
WC Tested Up To: 4.0.1
Stable Tag: 1.0.0

GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, Depth, and Volume for WooCommerce Products and Variations.

== Description ==

Easily include the following information fields in the WooCommerce "Product data" metabox and in the purchase page "Additional information" section:

* GTIN
* GTIN-8
* GTIN-12 (UPC)
* GTIN-13 (EAN)
* GTIN-14
* ISBN
* MPN
* Depth
* Volume

The SSO &gt; WooCommerce Metadata settings allow you to enable or disable information fields with simple checkboxes, along with customizing the labels and placeholders for each WordPress language (aka locale).

The enabled WooCommerce product information fields are seamlessly added to the WooCommerce product editing metabox (aka "Product data") for products and their variations.

The [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/) will automatically include the following product meta tags for enabled product information fields:

* product:ean
* product:isbn
* product:mfr_part_no
* product:upc

The [Schema JSON-LD Markup add-on]((https://wordpress.org/plugins/wpsso-schema-json-ld/) will automatically include the following properties for enabled product information fields:

* Schema Product:
	* mpn
	* gtin14
	* gtin13
	* gtin12
	* gtin8
	* gtin
	* productID (isbn)
	* depth
	* volume

<h3>WPSSO Core Plugin Required</h3>

WPSSO Metadata for WooCommerce (aka WPSSO WCMD) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/).

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Metadata for WooCommerce add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/install-the-plugin/).
* [Uninstall the WPSSO Metadata for WooCommerce add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

<h3 class="top">Frequently Asked Questions</h3>

* None.

== Screenshots ==

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

**Version 1.0.0-dev.4 (2020/04/28)**

* **New Features**
	* Version 1.0.0-dev.4 release.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.2.
	* WPSSO Core v7.2.0.

== Upgrade Notice ==

= 1.0.0-dev.4 =

(2020/04/28) Version 1.0.0-dev.4 release.

