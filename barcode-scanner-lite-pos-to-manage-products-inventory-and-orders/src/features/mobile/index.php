<?php

use UkrSolution\BarcodeScanner\API\classes\Users;
use UkrSolution\BarcodeScanner\features\settings\Settings;

$settings = new Settings();


$usbs = $jsData && isset($jsData['usbs']) ? $jsData['usbs'] : array();
$usbsCustomCss = $jsData && isset($jsData['usbsCustomCss']) ? $jsData['usbsCustomCss'] : array();
$usbsHistory = $jsData && isset($jsData['usbsHistory']) ? $jsData['usbsHistory'] : array();
$usbsUserCF = $jsData && isset($jsData['usbsUserCF']) ? $jsData['usbsUserCF'] : array();
$usbsOrderCF = $jsData && isset($jsData['usbsOrderCF']) ? $jsData['usbsOrderCF'] : array();
$userFormCF = $jsData && isset($jsData['userFormCF']) ? $jsData['userFormCF'] : array();
$usbsWooShippmentProviders = $jsData && isset($jsData['usbsWooShippmentProviders']) ? $jsData['usbsWooShippmentProviders'] : array();
$usbsLangs = $jsData && isset($jsData['usbsLangs']) ? $jsData['usbsLangs'] : array();
$usbsLangsApp = $jsData && isset($jsData['usbsLangsApp']) ? $jsData['usbsLangsApp'] : array();
$usbsInterface = $jsData && isset($jsData['usbsInterface']) ? $jsData['usbsInterface'] : array();
$cartExtraData = $jsData && isset($jsData['cartExtraData']) ? $jsData['cartExtraData'] : array();

$userId = $usbs && isset($usbs['userId']) ? $usbs['userId'] : "";
$userRole = $userId ? Users::getUserRole($userId) : '';
?>
<title>Barcode Scanner mobile</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<div id="refreshIndicator" style="display: none; text-align: center; background: #fff; color: #434343; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: 0.3s; overflow: hidden; height: 0;">
    <?php echo esc_html__("Refreshing...", "us-barcode-scanner"); ?>
</div>
<a href="#barcode-scanner-mobile"></a>
<div id="ukrsolution-barcode-scanner"></div>
<div id="ukrsolution-barcode-scanner-mobile"></div>

<div id="barcode-scanner-mobile-preloader" data-role="<?php echo esc_attr($userRole) ?>">
    <div style="user-select: none;">Loading...</div>
</div>
<style class="usbs-style">
    <?php echo wp_kses_post($customCssMobile); ?>
</style>
<script>
    window.barcodeScannerStartAppAuto = true;
</script>
<script>
    window.usbsLangsMobile = <?php echo json_encode($usbsLangs); ?>;
</script>
<script>
    window.usbsLangsMobileApp = <?php echo json_encode($usbsLangsApp); ?>;
</script>
<script>
    window.usbsInterfaceMobile = <?php echo json_encode(apply_filters("scanner_product_fields_filter", $usbsInterface)); ?>;
</script>
<script>
    window.usbsMobile = <?php echo json_encode($usbs); ?>;
</script>
<script>
    window.usbsHistory = <?php echo json_encode($usbsHistory); ?>;
</script>
<script>
    window.usbsUserCF = <?php echo json_encode($usbsUserCF); ?>;
</script>
<script>
    window.usbsOrderCF = <?php echo json_encode($usbsOrderCF); ?>;
</script>
<script>
    window.userFormCF = <?php echo json_encode($userFormCF); ?>;
</script>
<script>
    window.usbsWooShippmentProviders = <?php echo json_encode($usbsWooShippmentProviders); ?>;
</script>
<script>
    window.cartExtraData = <?php echo json_encode($cartExtraData); ?>;
</script>

<script>
    <?php
    $field = $settings->getSettings("modifyPreProcessSearchString");
    $fnContent = $field === null ? "" : trim($field->value);

    if ($fnContent) {
        echo wp_kses_post("window.usbsModifyPreProcessSearchString = function (bs_search_string) {" . $fnContent . " ; \n return bs_search_string; };");
    } ?>
</script>

<script>
    window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {
        console.error("Error occurred: " + lineNumber + "-> " + errorMsg);
        return false;
    };

    document.addEventListener("DOMContentLoaded", function() {
        window.addEventListener("message", WebbsSettingsMessages, false);
    });

    function WebbsSettingsMessages(event) {
        switch (event.data.message) {
            case "iframe.checkResult":
            const el = document.getElementById("bs-check-license-message");
            if (el) el.innerHTML = event.data.resultMessage;
            break;
            case "mobile.postMessage":
            BarcodeScannerMobileBridge(event.data);
            break;
        }
    }

    function bsMobileEmitMessages(data) {
        window.postMessage(data, "*");  
        return { accepted: true };
    }

    function checkWebViewConnection(data) {
        return data;
    }

    function checkWebViewReactConnection(data) {
        if (window.bsCheckWebViewReactConnection) return window.bsCheckWebViewReactConnection(data);
    }

    var BarcodeScannerMobileBridge = function (data) {
        if (navigator.share) {
            navigator
            .share(data)
            .then(() => console.log("Successful share"))
            .catch((error) => {
                console.error("Error sharing " + error);
                if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.ReactNativeWebView) {
                window.webkit.messageHandlers.ReactNativeWebView.postMessage(JSON.stringify(data))
                } else if (window.ReactNativeWebView) {
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
                }
            });
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.ReactNativeWebView) {
            window.webkit.messageHandlers.ReactNativeWebView.postMessage(JSON.stringify(data))
        } else if (window.ReactNativeWebView) {
            window.ReactNativeWebView.postMessage(JSON.stringify(data));
        } else if (window.JSBridge && window.JSBridge.message) {
            window.JSBridge.message(JSON.stringify(data));
        } else {
            console.warn("web share not supported");
        }
    };
</script>

<script>
    if (!window.usbs && window.usbsMobile) window.usbs = window.usbsMobile;

    var WebBarcodeScannerOpen = function (event) {
        const href = event.target.getAttribute("href");
        const postId = event.target.getAttribute("usbs-order-open-post");

        window.postMessage(JSON.stringify({ message: "element-click", href, postId }), "*");

        if (!window.usbsMobile || !window.usbsMobile.platform) {
            const bodyEl = document.querySelector("body");
            bodyEl.classList.add("barcode-scanner-shows");
        }
    };

    var WebBarcodeScannerScripts = function () {
        try {
            
    const max_tries = 3;
    let tries = 0;
    function loadMainJS (url) {
      var appJs = document.createElement("script"); 
      appJs.type = "text/javascript"; 
      appJs.src = url;
      appJs.async = true;
      appJs.onload = () => { console.log("Loader: " + window.usbsMobile.appJsPath + " loaded"); };
      appJs.onerror = () => { 
        console.error("Loader: " + window.usbsMobile.appJsPath + " not loaded"); 

        if (tries < max_tries) {
          tries++;
          
          setTimeout(() => {
            loadMainJS(url);
          }, 2000);

          return;
        }
        
        window.parent.postMessage({
          message: "mobile.postMessage", method: "CMD_ALERT", options: {
              title: "JS Error", message: "Loader: " + window.usbsMobile.appJsPath + " not loaded", hideSystemInfo: false, restart: true, require: true, logout: false
          }
        }, "*");
      };
      document.body.appendChild(appJs);
    }
    loadMainJS(window.usbsMobile.appJsPath);
    
        } catch (error) {
            console.error("3. " + error.message);
        }   
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('link, style:not(.usbs-style)').forEach(el => el.remove());

        const link1 = document.createElement('link');
        link1.rel = 'preconnect';
        link1.href = 'https://fonts.googleapis.com';

        const link2 = document.createElement('link');
        link2.rel = 'preconnect';
        link2.href = 'https://fonts.gstatic.com';
        link2.crossOrigin = ''; 

        const link3 = document.createElement('link');
        link3.href = 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap';
        link3.rel = 'stylesheet';

        document.body.appendChild(link1);
        document.body.appendChild(link2);
        document.body.appendChild(link3);

        const css = `
            *, body * { user-select: none; }
            .ukrsolution-barcode-scanner-frame, .ukrsolution-barcode-scanner-frame{ position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; border: none; }
            .ukrsolution-barcode-scanner-frame.closed, .ukrsolution-barcode-scanner-frame.closed{ display: none; }
            body.barcode-scanner-shows{ overflow: hidden; }
            #barcode-scanner-mobile-preloader { background: white; height: 100vh; width: 100vw; position: fixed; top: 0; left: 0; display: flex; justify-content: center; align-items: center; }
            `;
        const style = document.createElement("style");

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
        document.body.appendChild(style);

        let s = 'a[href="#barcode-scanner-mobile"]';
        let menu = document.querySelectorAll(s);

        const WebstartLoading = function (e) {
            try {
            e.preventDefault();
            e.stopPropagation();

            if (menu instanceof NodeList || Array.isArray(menu)) {
                menu.forEach(el => {
                el.addEventListener("click", function handler(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    WebBarcodeScannerOpen(e);
                });
                });
            } else if (menu) {
                menu.addEventListener("click", function handler(e) {
                e.preventDefault();
                e.stopPropagation();
                WebBarcodeScannerOpen(e);
                });
            }

            var ls = localStorage.getItem("barcode-scanner-v1");
            window.serializedData = ls ? ls : "{}";
            window.addEventListener(
                "message",
                function (event) {
                switch (event.data.message) {
                    case "USBS.localStorage.setItem":
                    localStorage.setItem(event.data.storageKey, event.data.serializedData);
                    break;
                    case "USBS.iframe.onload":
                    e.target.click();

                    const preloader = document.getElementById("barcode-scanner-mobile-preloader");
                    if (preloader) {
                        preloader.style.display = "none";
                    }

                                        break;
                }
                },
                false
            );
            } catch (error) {
            console.error("1. " + error.message);
            }

            return false;

        };

        WebBarcodeScannerScripts();

        function WebBarcodeScannerClickHandler(e) {
            WebstartLoading(e);
        }

                if (menu instanceof NodeList || Array.isArray(menu)) {
            menu.forEach(el => {
            el.removeEventListener("click", WebBarcodeScannerClickHandler);
            el.addEventListener("click", WebBarcodeScannerClickHandler);
            el.click();
            });
        } else if (menu) {
            menu.removeEventListener("click", WebBarcodeScannerClickHandler);
            menu.addEventListener("click", WebBarcodeScannerClickHandler);
            menu.click();
        }
    });
</script>