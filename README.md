<h1>Metadata for WooCommerce</h1><h3>WPSSO Add-on</h3>

<table>
<tr><th align="right" valign="top" nowrap>Plugin Name</th><td>WPSSO Metadata for WooCommerce</td></tr>
<tr><th align="right" valign="top" nowrap>Summary</th><td>GTIN, GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN), GTIN-14, ISBN, MPN, Depth, and Volume for WooCommerce Products and Variations.</td></tr>
<tr><th align="right" valign="top" nowrap>Stable Version</th><td>1.0.0</td></tr>
<tr><th align="right" valign="top" nowrap>Requires PHP</th><td>5.6 or newer</td></tr>
<tr><th align="right" valign="top" nowrap>Requires WordPress</th><td>4.2 or newer</td></tr>
<tr><th align="right" valign="top" nowrap>Tested Up To WordPress</th><td>5.4.1</td></tr>
<tr><th align="right" valign="top" nowrap>Tested Up To WooCommerce</th><td>4.0.1</td></tr>
<tr><th align="right" valign="top" nowrap>Contributors</th><td>jsmoriss</td></tr>
<tr><th align="right" valign="top" nowrap>License</th><td><a href="https://www.gnu.org/licenses/gpl.txt">GPLv3</a></td></tr>
<tr><th align="right" valign="top" nowrap>Tags / Keywords</th><td>woocommerce, gtin, upc, ean, mpn, isbn, metadata, attribute, information, product, custom field</td></tr>
</table>

<h2>Description</h2>

<p>Easily include the following information in the WooCommerce "Product data" metabox and "Additional information" purchase page section:</p>

<ul>
<li>GTIN</li>
<li>GTIN-8</li>
<li>GTIN-12 (UPC)</li>
<li>GTIN-13 (EAN)</li>
<li>GTIN-14</li>
<li>ISBN</li>
<li>MPN</li>
<li>Depth</li>
<li>Volume</li>
</ul>

<p>Adds a WooCommerce &gt; Settings &gt; Products &gt; Fluid volume unit option:</p>

<ul>
<li>millilitre</li>
<li>centilitre</li>
<li>liter</li>
<li>kiloliter</li>
<li>teaspoon</li>
<li>tablespoon</li>
<li>fluid ounce</li>
<li>US pint</li>
<li>US quart</li>
<li>US gallon</li>
</ul>

<p>The information shown under the "Additional information" section on the WooCommerce purchase page changes as different variations are selected, just like the default Weight and Dimensions information managed by WooCommerce.</p>

<p>The SSO &gt; WooCommerce Metadata settings page allows you to enable or disable product metadata with simple checkboxes, along with customizing the labels and placeholders for different WordPress languages (aka locales).</p>

<p>The enabled product metadata fields are added seamlessly to the WooCommerce product editing pages (aka the "Product data" metabox) for simple products and product variations.</p>

<p>The <a href="https://wordpress.org/plugins/wpsso/">WPSSO Core plugin</a> will automatically include the following product meta tags for enabled product metadata:</p>

<ul>
<li>product:ean</li>
<li>product:isbn</li>
<li>product:mfr_part_no</li>
<li>product:upc</li>
</ul>

<p>The [Schema JSON-LD Markup add-on]((https://wordpress.org/plugins/wpsso-schema-json-ld/) will automatically include the following properties for enabled product metadata:</p>

<ul>
<li>Schema Product:

<ul>
<li>mpn</li>
<li>gtin14</li>
<li>gtin13</li>
<li>gtin12</li>
<li>gtin8</li>
<li>gtin</li>
<li>productID isbn</li>
<li>depth</li>
<li>additionalProperty fluid_volume</li>
</ul></li>
</ul>

<h3>WPSSO Core Plugin Required</h3>

<p>WPSSO Metadata for WooCommerce (aka WPSSO WCMD) is an add-on for the <a href="https://wordpress.org/plugins/wpsso/">WPSSO Core plugin</a>.</p>

<p>Additional product information for Open Graph meta tags and Schema Product properties requires the WooCommerce integration module provided with the <a href="https://wpsso.com/">WPSSO Core Premium plugin</a>.</p>


<h2>Installation</h2>

<h3 class="top">Install and Uninstall</h3>

<ul>
<li><a href="https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/install-the-plugin/">Install the WPSSO Metadata for WooCommerce add-on</a>.</li>
<li><a href="https://wpsso.com/docs/plugins/wpsso-wc-metadata/installation/uninstall-the-plugin/">Uninstall the WPSSO Metadata for WooCommerce add-on</a>.</li>
</ul>


<h2>Frequently Asked Questions</h2>

<h3 class="top">Frequently Asked Questions</h3>

<ul>
<li>None.</li>
</ul>


