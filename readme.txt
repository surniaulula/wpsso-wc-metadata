=== WPSSO Product Metadata for WooCommerce SEO - MPN, ISBN, GTIN, UPC, EAN, Global Identifiers ===
Plugin Name: WPSSO Product Metadata for WooCommerce SEO
Plugin Slug: wpsso-wc-metadata
Text Domain: wpsso-wc-metadata
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-wc-metadata/assets/
Tags: woocommerce, gtin, upc, ean, isbn, mpn, custom fields, global identifier, manufacturer part number
Contributors: jsmoriss
Requires Plugins: wpsso, woocommerce
Requires PHP: 7.2
Requires At Least: 5.2
Tested Up To: 6.1.1
WC Tested Up To: 7.2.2
Stable Tag: 2.0.1

MPN, ISBN, GTIN, GTIN-8, UPC, EAN, GTIN-14, net dimensions, and fluid volume for WooCommerce products and variations.

== Description ==

<!-- about -->

Provides additional fields in the WooCommerce Product data metabox and the product webpage Additional information section:

MPN (Manufacturer Part Number)
ISBN
GTIN-14
GTIN-13 (EAN)
GTIN-12 (UPC)
GTIN-8
GTIN
Net Length / Depth
Net Width
Net Height
Net Weight
Fluid Volume

<!-- /about -->

The *SSO &gt; WooCommerce Metadata* settings page allows you to enable or disable each product metadata, along with customizing the label and placeholder values for different languages (aka WordPress locales).

The product global identifier values (ie. MPN, ISBN, GTIN-14, GTIN-13, GTIN-12, GTIN-8, and GTIN) are searchable from both the front-end webpage, and the admin WooCommerce Products page.

The WPSSO Product Metadata for WooCommerce SEO add-on provides Schema (aka Schema.org) mpn, gtin14, gtin13, gtin12, gtin8, gtin, productID isbn, depth (aka length), width, height, weight, and additionalProperty fluid_volume values to the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/) for Google Rich Results, Rich Snippets, and Structured Data.

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
* gtin
* productID isbn
* depth (aka length)
* width
* height
* weight
* additionalProperty fluid_volume

<h3>WPSSO Core Required</h3>

WPSSO Product Metadata for WooCommerce SEO (WPSSO WCMD) is an add-on for [WooCommerce](https://wordpress.org/plugins/woocommerce/) and the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/), which provides complete structured data for WordPress to present your content at its best on social sites and in search results – no matter how URLs are shared, reshared, messaged, posted, embedded, or crawled.

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Product Metadata for WooCommerce SEO add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/install-the-plugin/).
* [Uninstall the WPSSO Product Metadata for WooCommerce SEO add-on](https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/uninstall-the-plugin/).

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

<p><strong>WPSSO Core Premium customers have access to development, alpha, beta, and release candidate version updates:</strong></p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" (for example) version filter for the WPSSO Core plugin and/or its add-ons. When new development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can reselect the "Stable / Production" version filter at any time to reinstall the latest stable version.</p>

<h3>Changelog / Release Notes</h3>

**Version 3.0.0-dev.1 (TBD)**

* **New Features**
	* Added a "Product Net Dimensions" option in the SSO &gt; WC Metadata settings page.
* **Improvements**
	* Added a "Show" column in the SSO &gt; WC Metadata settings page to enable/disable metadata for editing pages and front-end pages independently.
	* Renamed the "Enable" column in the SSO &gt; WC Metadata settings page to "Edit".
* **Bugfixes**
	* Fixed the missing units value under the Additional Information tab when switching variations.
* **Developer Notes**
	* Added new methods:
		* `WpssoWcmdConfig::is_editable()`
		* `WpssoWcmdConfig::is_showable()`
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v14.4.0-dev.1.
	* WooCommerce v5.0.

**Version 2.0.1 (2022/12/28)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Fixed a minor syntax issue when sanitizing for enabled and empty custom field names.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v14.0.0.
	* WooCommerce v5.0.

**Version 2.0.0 (2022/12/28)**

* **New Features**
	* Added new product metadata options:
		* Net Len. / Depth
		* Net Width
		* Net Height
		* Net Weight
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Added a new `WpssoWcmdFiltersOptions` class.
	* Added a new `WpssoWcmdFiltersUpgrade` class
	* Refactored the `WpssoWcmdConfig->get_md_config()` method and its config array.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v14.0.0.
	* WooCommerce v5.0.

**Version 1.13.2 (2022/05/23)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Fixed the admin product search query to match variations, but avoid including variations in the product search results list.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v12.3.0.
	* WooCommerce v5.0.

**Version 1.13.1 (2022/03/07)**

* **New Features**
	* None.
* **Improvements**
	* Shortened the SSO menu item name from "WooCommerce Metadata" to "WC Metadata".
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v11.5.0.
	* WooCommerce v5.0.

**Version 1.13.0 (2022/01/19)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Renamed the lib/abstracts/ folder to lib/abstract/.
	* Renamed the `SucomAddOn` class to `SucomAbstractAddOn`.
	* Renamed the `WpssoAddOn` class to `WpssoAbstractAddOn`.
	* Renamed the `WpssoWpMeta` class to `WpssoAbstractWpMeta`.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.14.0.
	* WooCommerce v5.0.

**Version 1.12.0 (2022/01/11)**

* **New Features**
	* None.
* **Improvements**
	* For comformity, disable the product attribute names of enabled and disabled metadata (ie. custom fields) instead of just the enabled ones.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.13.0.
	* WooCommerce v5.0.

**Version 1.11.0 (2021/12/16)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Renamed the disabled option key suffix from `":is" = "disabled"` to `":disabled" = true`.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.12.0.
	* WooCommerce v5.0.

**Version 1.10.2 (2021/11/16)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Refactored the `SucomAddOn->get_missing_requirements()` method.
* **Requires At Least**
	* PHP v7.2.
	* WordPress v5.2.
	* WPSSO Core v9.8.0.
	* WooCommerce v5.0.

**Version 1.10.1 (2021/10/06)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Standardized `get_table_rows()` calls and filters in 'submenu' and 'sitesubmenu' classes.
* **Requires At Least**
	* PHP v7.0.
	* WordPress v5.0.
	* WPSSO Core v9.1.0.
	* WooCommerce v3.8.0.

**Version 1.10.0 (2021/09/24)**

Maintenance release for WPSSO Core v9.0.0.

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.0.
	* WordPress v5.0.
	* WPSSO Core v9.0.0.
	* WooCommerce v3.8.0.

**Version 1.9.0 (2021/08/27)**

* **New Features**
	* None.
* **Improvements**
	* Added a filter hook to enable the "Additional information" tab if the product or its variations has a metadata value.
* **Bugfixes**
	* Fix to show product metadata if the main product value is empty but one or more variations has a value.
* **Developer Notes**
	* Added checks with `WpssoUtilWoocommerce->has_meta()` to include/exclude showing product metadata under the "Additional information" tab.
* **Requires At Least**
	* PHP v7.0.
	* WordPress v5.0.
	* WPSSO Core v8.36.1.
	* WooCommerce v3.8.0.

**Version 1.8.1 (2021/02/25)**

* **New Features**
	* None.
* **Improvements**
	* Updated the banners and icons of WPSSO Core and its add-ons.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v7.0.
	* WordPress v5.0.
	* WPSSO Core v8.34.0.
	* WooCommerce v3.8.0.

**Version 1.8.0 (2020/11/30)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* Fixed a "Call to a member function get_enabled_metadata_key() on null" error.
* **Developer Notes**
	* Included the `$addon` argument for library class constructors.
* **Requires At Least**
	* PHP v7.0.
	* WordPress v4.5.
	* WPSSO Core v8.16.0.
	* WooCommerce v3.8.0.

**Version 1.7.0 (2020/11/29)**

* **New Features**
	* None.
* **Improvements**
	* Updated information and help messages in the SSO &gt; WooCommerce Metadata settings page.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.4.
	* WPSSO Core v8.14.0.
	* WooCommerce v3.6.4.

**Version 1.6.1 (2020/10/17)**

* **New Features**
	* None.
* **Improvements**
	* Refactored the add-on class to extend a new WpssoAddOn abstract class.
* **Bugfixes**
	* Fixed backwards compatibility with older 'init_objects' and 'init_plugin' action arguments.
* **Developer Notes**
	* Added a new WpssoAddOn class in lib/abstracts/add-on.php.
	* Added a new SucomAddOn class in lib/abstracts/com/add-on.php.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.4.
	* WPSSO Core v8.13.0.
	* WooCommerce v3.6.4.

== Upgrade Notice ==

= 3.0.0-dev.1 =

(TBD) Added a "Product Net Dimensions" option and "Show" column in the SSO &gt; WC Metadata settings page.

= 2.0.1 =

(2022/12/28) Fixed a minor syntax issue when sanitizing for enabled and empty custom field names.

= 2.0.0 =

(2022/12/28) Added new product metadata options.

= 1.13.2 =

(2022/05/23) Fixed the admin product search query to match variations, but avoid including variations in the product search results list.

= 1.13.1 =

(2022/03/07) Shortened the SSO menu item name from "WooCommerce Metadata" to "WC Metadata".

= 1.13.0 =

(2022/01/19) Renamed the lib/abstracts/ folder and its classes.

= 1.12.0 =

(2022/01/11) For comformity, disable the product attribute names of enabled and disabled metadata (ie. custom fields) instead of just the enabled ones.

= 1.11.0 =

(2021/12/16) Renamed the disabled option key suffix.

= 1.10.2 =

(2021/11/16) Refactored the `SucomAddOn->get_missing_requirements()` method.

= 1.10.1 =

(2021/10/06) Standardized `get_table_rows()` calls and filters in 'submenu' and 'sitesubmenu' classes.

= 1.10.0 =

(2021/09/24) Maintenance release for WPSSO Core v9.0.0.

= 1.9.0 =

(2021/08/27) Added a filter hook to enable the "Additional information" tab. Fix to show product metadata if the main product value is empty.

= 1.8.1 =

(2021/02/25) Updated the banners and icons of WPSSO Core and its add-ons.

= 1.8.0 =

(2020/11/30) Fixed a "Call to a member function get_enabled_metadata_key() on null" error. Included the `$addon` argument for library class constructors.

= 1.7.0 =

(2020/11/29) Updated information and help messages in the SSO &gt; WooCommerce Metadata settings page.

= 1.6.1 =

(2020/10/17) Refactored the add-on class to extend a new WpssoAddOn abstract class.

