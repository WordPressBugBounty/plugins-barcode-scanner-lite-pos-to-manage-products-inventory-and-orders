<?php
$tabs = array(
    'store-tab' => array(
        'name' => __('Store tab', "us-barcode-scanner"),
        'shortcodes' => array(
            __('Name of the store', "us-barcode-scanner") => '[store-name]',
            __('Postcode of the store', "us-barcode-scanner") => '[store-postcode]',
            __('Address of the store', "us-barcode-scanner") => '[store-address]',
            __('Address 2 of the store', "us-barcode-scanner") => '[store-address-2]',
            __('Country of the store', "us-barcode-scanner") => '[store-country]',
            __('State of the store', "us-barcode-scanner") => '[store-state]',
            __('City of the store', "us-barcode-scanner") => '[store-city]',
        ),
        'blocks' => array()
    ),
    'products-tab' => array(
        'name' => __('Products tab', "us-barcode-scanner"),
        'shortcodes' => array(
            __('Product name', "us-barcode-scanner") => '[product-name]',
            __('Product SKU', "us-barcode-scanner") => '[product-sku]',
            __('Purchased product quantity', "us-barcode-scanner") => '[order-product-qty]',
            __('Product price for 1 item', "us-barcode-scanner") => '[item-price]',
            __('Product price for 1 item with tax', "us-barcode-scanner") => '[item-price+tax]',
            __('Product tax for 1 item', "us-barcode-scanner") => '[item-tax]',
            __('Product price for all the same items', "us-barcode-scanner") => '[item-price-total]',
            __('Product price for all the same items', "us-barcode-scanner") => '[item-price+tax-total]',
            __('Product tax for all the same items', "us-barcode-scanner") => '[item-tax-total]',
            __('Pull data from the product custom/meta field', "us-barcode-scanner") => '[custom-field=XXXX]',
            __('Pull data from the main product custom/meta field', "us-barcode-scanner") => '[custom-field-parent=XXXX]',
        ),
        'blocks' => array(
            array(
                'name' => __('List of the purchased items:', "us-barcode-scanner"),
                'text' => "<table style='width: 100%;font-size: 12px'>
                [product-list-start test-products=10]
                    <tr>
                        <td style='width: 100%'>[product-name] | [product-sku]</td>
                        <td style='padding-right: 1mm'>[order-product-qty] x [item-price]</td>
                        <td>[item-price-total]</td>
                    </tr>
                [product-list-end]
                </table>"
            )
        ),
    ),
    'order-tab' => array(
        'name' => __('Order tab', "us-barcode-scanner"),
        'shortcodes' => array(
            __('Order Id', "us-barcode-scanner") => '[order-id]',
            __('Order shipping price', "us-barcode-scanner") => '[order-shipping]',
            __('Order shipping tax', "us-barcode-scanner") => '[order-shipping-tax]',
            __('Order subtotal price', "us-barcode-scanner") => '[order-subtotal]',
            __('Order subtotal tax', "us-barcode-scanner") => '[order-subtotal-tax]',
            __('Order tax', "us-barcode-scanner") => '[order-tax]',
            __('Order total price', "us-barcode-scanner") => '[order-total]',
            __('Order discount', "us-barcode-scanner") => '[order-discount before="Discount:" show-value="false"]',
            __('Order date', "us-barcode-scanner") => '[order-date format="DD.MM.YYYY HH:mm"]',
            __('Pull data from the order custom/meta field', "us-barcode-scanner") => '[custom-field=XXXX]',
            "<b>" . __('Billing:', 'us-barcode-scanner') . "</b>" => '',
            __('Order billing first name', "us-barcode-scanner") => '[order-billing-first-name]',
            __('Order billing last name', "us-barcode-scanner") => '[order-billing-last-name]',
            __('Order billing country', "us-barcode-scanner") => '[order-billing-country]',
            __('Order billing state', "us-barcode-scanner") => '[order-billing-state]',
            __('Order billing city', "us-barcode-scanner") => '[order-billing-city]',
            __('Order billing address 1', "us-barcode-scanner") => '[order-billing-address1]',
            __('Order billing address 2', "us-barcode-scanner") => '[order-billing-address2]',
            __('Order billing postal code', "us-barcode-scanner") => '[order-billing-postal-code]',
            __('Order billing company', "us-barcode-scanner") => '[order-billing-company]',
            __('Order billing phone', "us-barcode-scanner") => '[order-billing-phone]',
            __('Order billing email', "us-barcode-scanner") => '[order-billing-email]',
            "<b>" . __('Shipping:', 'us-barcode-scanner') . "</b>" => '',
            __('Order shipping first name', "us-barcode-scanner") => '[order-shipping-first-name]',
            __('Order shipping last name', "us-barcode-scanner") => '[order-shipping-last-name]',
            __('Order shipping country', "us-barcode-scanner") => '[order-shipping-country]',
            __('Order shipping state', "us-barcode-scanner") => '[order-shipping-state]',
            __('Order shipping city', "us-barcode-scanner") => '[order-shipping-city]',
            __('Order shipping address 1', "us-barcode-scanner") => '[order-shipping-address1]',
            __('Order shipping address 2', "us-barcode-scanner") => '[order-shipping-address2]',
            __('Order shipping postal code', "us-barcode-scanner") => '[order-shipping-postal-code]',
            __('Order shipping company', "us-barcode-scanner") => '[order-shipping-company]',
            __('Order shipping phone', "us-barcode-scanner") => '[order-shipping-phone]',
            "<b>" . __('Taxes:', 'us-barcode-scanner') . "</b>" => '',
        ),
        'blocks' => array(
            array(
                'name' => __('List of the applied taxes:', "us-barcode-scanner"),
                'text' => "<table style='width: 100%;font-size: 12px'>
                [order-taxes-list-start test-taxes=3]
                    <tr>
                        <td>[tax-label]</td>
                        <td style='text-align: right'>[tax-cost]</td>
                    </tr>
                [order-taxes-list-end]
                </table>"
            )
        ),
    ),
)
?>
<div class="receipt-documentation-modal" data-rdm-wrapper="1" style="display: none;">
    <div class="rdm-content">
        <div class="rdm-header">
            <div></div>
            <span class="rdm-close">
                <svg class="MuiSvgIcon-root" focusable="false" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                </svg>
            </span>
        </div>
        <div class="rdm-body">
            <div class="rdm-tabs">
                <?php foreach ($tabs as $key => $tabValue) : ?>
                    <div data-rdm-tab="<?php echo esc_attr($key); ?>" class="<?php echo $key === "store-tab" ? esc_html('active') : '' ?>"><?php echo esc_html($tabValue['name']); ?></div>
                <?php endforeach; ?>
            </div>
            <div class="rdm-tabs-content">
                <?php foreach ($tabs as $key => $tabValue) : ?>
                    <div data-rdm-tab="<?php echo esc_attr($key); ?>" class="<?php echo $key === "store-tab" ? esc_html('active') : '' ?>" style="max-height: 500px; overflow-y: auto;">
                        <?php foreach ($tabValue['shortcodes'] as $shortcode => $label) : ?>
                            <div>
                                <div><?php echo wp_kses_post($shortcode) ?></div>
                                <div><?php echo esc_html($label) ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($tabValue['blocks'] as $blockValue) : ?>
                            <div>
                                <div><?php echo esc_html($blockValue['name']) ?></div>
                                <div><?php echo str_replace("\n", "<br/>", esc_html($blockValue['text'])) ?></div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .receipt-documentation-modal {
        display: none;
        position: fixed;
        width: 100vw;
        height: 100vh;
        background: #00000024;
        top: 0;
        left: 0;
        z-index: 999999999;
        align-items: center;
        justify-content: center;
    }

    .receipt-documentation-modal.show {
        display: flex;
    }

    .receipt-documentation-modal .rdm-content {
        background: #fff;
        position: relative;
        padding: 15px 20px;
        margin: 15px;
        border-radius: 4px;
        width: 100%;
        min-width: 400px;
        max-width: 700px;
    }

    .receipt-documentation-modal .rdm-content * {
        user-select: text;
    }

    .rdm-header {
        display: flex;
        justify-content: space-between;
    }

    .rdm-body {
        display: flex;
        flex-direction: column;
    }

    .rdm-close {
        display: inline-block;
        width: 20px;
        height: 20px;
        cursor: pointer;
        position: absolute;
        top: 5px;
        right: 5px;
    }

    .rdm-tabs {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e7e7e7;
    }

    .rdm-tabs>div {
        background: #dcdcde;
        cursor: pointer;
        padding: 5px 10px;
        margin-right: 2px;
    }

    .rdm-tabs>div.active {
        background: #f0f0f1;
    }

    .rdm-tabs-content>div {
        display: none;
    }

    .rdm-tabs-content div[data-rdm-tab] {
        display: none;
        flex-direction: column;
    }

    .rdm-tabs-content div[data-rdm-tab].active {
        display: flex;
    }

    .rdm-tabs-content div[data-rdm-tab]>div {
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        padding: 2px 5px;
    }

    .rdm-tabs-content div[data-rdm-tab]>div>div:first-child {
        width: 45%;
        min-width: 45%;
    }

    .rdm-block {
        display: flex;
    }
</style>
<script>
    let rdmStatus = false;
    let rdmActiveTab = 'store-tab';

    const modalToggle = (e) => {
        if (e) e.preventDefault();

        jQuery('.receipt-documentation-modal').removeAttr("style");

        if (rdmStatus) {
            jQuery('.receipt-documentation-modal').removeClass("show");
            jQuery('body').css("overflow-y", 'initial');
        } else {
            jQuery('.receipt-documentation-modal').addClass("show");
            jQuery('body').css("overflow-y", 'hidden');
        }

        rdmStatus = !rdmStatus;
    }

    jQuery("#receipt-documentation-toggle").click(modalToggle);
    jQuery(".rdm-close").click(modalToggle);

    jQuery(".receipt-documentation-modal").click((e => {
        if (jQuery(e.target).attr('data-rdm-wrapper')) modalToggle();
    }));

    jQuery(".rdm-tabs>div").click((e) => {
        e.preventDefault();

        const tab = jQuery(e.target).attr('data-rdm-tab');
        rdmActiveTab = tab;

        jQuery(".rdm-tabs>div").removeClass('active');
        jQuery(e.target).addClass('active');

        jQuery(".rdm-tabs-content>div").removeClass('active');
        jQuery(".rdm-tabs-content>div[data-rdm-tab='" + tab + "']").addClass('active');
    });
</script>