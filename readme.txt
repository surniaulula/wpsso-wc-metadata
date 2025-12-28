=== WPSSO Schema Product Metadata for WooCommerce ===
Plugin Name: WPSSO Schema Product Metadata for WooCommerce
Plugin Slug: wpsso-wc-metadata
Text Domain: wpsso-wc-metadata
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-wc-metadata/assets/
Tags: schema, upc, ean, isbn, mpn
Contributors: jsmoriss
Requires Plugins: wpsso, woocommerce
Requires PHP: 7.4.33
Requires At Least: 6.0
Tested Up To: 6.9
WC Tested Up To: 10.4.3
Stable Tag: 6.0.0

MPN, ISBN, GTIN-8, UPC, EAN, GTIN-14, net dimensions, and fluid volume for better WooCommerce Schema markup.

== Description ==

<!-- about -->

Provides additional input fields in the WooCommerce "Product data" metabox and under the product webpage "Additional information" section:

* MPN (Manufacturer Part Number)
* ISBN
* GTIN-14
* GTIN-13 (EAN)
* GTIN-12 (UPC)
* GTIN-8
* Net Length / Depth
* Net Width
* Net Height
* Net Weight
* Fluid Volume

**Fully compatible with the WooCommerce "GTIN, UPC, EAN, or ISBN" input field** - extends the basic WooCommerce global unique ID feature with several additional, and more precise, global unique ID input fields.

<!-- /about -->

The *SSO &gt; WC Metadata* settings page allows you to enable or disable each product metadata (aka custom field), along with customizing the label and placeholder values for the available languages (aka WordPress locales).

The product global identifier values (ie. MPN, ISBN, GTIN-14, GTIN-13, GTIN-12, and GTIN-8) are searchable from the front-end webpage and the admin WooCommerce Products page.

The WPSSO Schema Product Metadata for WooCommerce add-(aka WPSSO WCMD) on provides Schema (aka Schema.org) mpn, gtin14, gtin13, gtin12, gtin8, gtin, productID isbn, depth (aka length), width, height, weight, and additionalProperty fluid_volume values for Google Rich Results, Rich Snippets, and Structured Data.

<h3>Includes WooCommerce Fluid Volume Units</h3>

Includes a **Fluid volume unit** option in the *WooCommerce &gt; Settings &gt; Products* settings page:

* ml
* cl
* l
* US tsp
* US tbsp
* US fl oz
* US cup
* US pt
* US qt
* US gal

<h3>Additonal Meta Tags and Schema Markup</h3>

Automatically provides additional Open Graph product meta tags for enabled product metadata:

* product:ean
* product:isbn
* product:mfr_part_no
* product:upc
* product:weight:value
* product:weight:units

Automatically provides additional Schema Product and Offer properties for enabled product metadata:

* mpn
* gtin14
* gtin13
* gtin12
* gtin8
* productID isbn
* depth (aka length)
* width
* height
* weight
* additionalProperty fluid_volume

<h3>WPSSO Core Required</h3>

WPSSO Schema Product Metadata for WooCommerce is an add-on for [WooCommerce](https://wordpress.org/plugins/woocommerce/) and the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which creates extensive and complete structured data to present your content at its best for social sites and search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Schema Product Metadata for WooCommerce add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/install-the-plugin/).
* [Uninstall the WPSSO Schema Product Metadata for WooCommerce add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/uninstall-the-plugin/).

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

* {major} = Major structural code changes and/or incompatible API changes (ie. breaking changes).
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).

<h3>Standard Edition Repositories</h3>

* [GitHub](https://surniaulula.github.io/wpsso-wc-metadata/)
* [WordPress.org](https://plugins.trac.wordpress.org/browser/wpsso-wc-metadata/)

<h3>Development Version Updates</h3>

<p><strong>WPSSO Core Premium edition customers have access to development, alpha, beta, and release candidate version updates:</strong></p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" (for example) version filter for the WPSSO Core plugin and/or its add-ons. When new development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can reselect the "Stable / Production" version filter at any time to reinstall the latest stable version.</p>

<p><strong>WPSSO Core Standard edition users (ie. the plugin hosted on WordPress.org) have access to <a href="https://wordpress.org/plugins/wpsso-wc-metadata/advanced/">the latest development version under the Advanced Options section</a>.</strong></p>

<h3>Changelog / Release Notes</h3>

**Version 6.0.0 (2025/12/25)**

Note that WPSSO WCMD requires WooCommerce 9.2+.

Removed the "GTIN" input field as WooCommerce 9.2+ offers a new "GTIN, UPC, EAN, or ISBN" input field. The MPN, ISBN, GTIN-14, GTIN-13 (EAN), GTIN-12 (UPC), and GTIN-8 input fields from the WPSSO WCMD add-on are still preferred, as they allow for multiple globally unique IDs (not just one) to be included in the Schema Product markup. You can enable all WPSSO WCMD input fields from the SSO &gt; WC Metadata settings page.

* **New Features**
	* None.
* **Improvements**
	* Moved main product dimensions and volume input fields (ie. Net Product Length/Width/Height/Weight/Fluid Volume) after the WooCommerce "GTIN, UPC, EAN, or ISBN" input field.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v6.0.
	* WPSSO Core v21.11.2.
	* WooCommerce v9.2.0.

**Version 5.1.0 (2025/12/24)**

* **New Features**
	* None.
* **Improvements**
	* No longer changes the custom field option value if the input field is disabled.
	* Updated the default "Product GTIN" custom field name from '_wpsso_product_gtin' to '_global_unique_id'.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.4.33.
	* WordPress v5.9.
	* WPSSO Core v21.11.1.
	* WooCommerce v6.0.0.

== Upgrade Notice ==

= 6.0.0 =

(2025/12/25) Removed the "GTIN" input field as WooCommerce 9.2+ offers a new "GTIN, UPC, EAN, or ISBN" input field. Moved main product dimensions and volume input fields after the WooCommerce "GTIN, UPC, EAN, or ISBN" input field.

= 5.1.0 =

(2025/12/24) No longer changes the custom field option value if the input field is disabled.

