<?php
$params = $_SERVER['QUERY_STRING'];
$params = preg_replace('/[^a-z0-9\.\:\/%&=]/i', '', $params);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>cookieBAR</title>
    <meta name="viewport" content="width=device-width">
    <meta name="description" content="cookieBAR is a free and easy solution to the EU cookie law. Paste one line of script tag and you're done. Even a kid could install it.">
    <meta name="keywords" content="cookie,localstorage,law,plugin,javascript,jquery,easy,solution">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.95.3/css/materialize.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/prism/0.1/prism.css">
    <style type="text/css">
        body {
            background: #f3f3f3;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        h2 {
            color: #444;
            letter-spacing: -4px;
        }
        h2 img {
            vertical-align: bottom;
        }
        code {
            word-break: break-word;
        }
        .counter {
            padding-left: 30px;
        }
        .main {
            flex: 1 0 auto;
            background: #FFF;
            margin: 30px auto 50px;
        }
        .collection a {
            display: inline-block;
            width: 40%;
        }
        .hidden {
            display: none;
        }
        @media only screen and (max-device-width: 800px) {
            .collection i {
                display: none;
            }
            .collection a {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main container z-depth-5">

        <div class='section'>

        <h2><a href='./'><img src="logo_big.png" alt="cookieBAR" /></a></h2>

        <h4>Privacy policy</h4>
            <p>In questa pagina si descrivono le modalità di gestione del presente sito internet in riferimento al trattamento dei dati personali degli utenti che lo consultano. Si tratta di un'informativa per il trattamento dei dati personali resa ai sensi dell'art. 13 Decreto legislativo 196/03 (di seguito Codice privacy) e "Individuazione delle modalità semplificate per l'informativa e l'acquisizione del consenso per l'uso dei cookie". L'informativa non è valida per altri siti web eventualmente consultabili attraverso i nostri links.</p>

            <strong>Titolare al trattamento dei dati personali</strong>
            <p>Emanuele Toscano</p>

            <strong>Luogo e finalità di trattamento dei dati</strong>
            <p>I trattamenti connessi ai servizi web di questo sito saranno effettuati esclusivamente dagli incaricati del trattamento ovvero da chi è materialmente incaricato di provvederne alla manutenzione. I dati personali forniti dagli utenti tramite le apposite maschere sono utilizzati al solo fine di eseguire il servizio o la prestazione richiesta e non sono comunicati a soggetti terzi salvo che la comunicazione sia imposta da obblighi di legge o sia strettamente necessario per l'adempimento delle richieste.</p>

            <strong>Trasferimento dei dati all'estero</strong>
            <p>I dati personali raccolti tramite il sito, potranno essere trasferiti al di fuori del territorio nazionale, solo ed esclusivamente per l'esecuzione dei servizi richiesti tramite il sito e nel rispetto delle specifiche disposizioni previste dal Codice.</p>

            <h5>Tipi di dati trattati:</h5>

            <strong>Dati di navigazione</strong>
            <p>I sistemi informatici e le procedure software preposte al funzionamento di questo sito web acquisiscono, nel corso del loro normale esercizio alcuni dati personali la cui trasmissione è implicita nell'uso dei protocolli di comunicazione di internet. Si tratta di informazioni che non sono raccolte per essere associate a interessati identificati, ma che per loro stessa natura potrebbero, attraverso elaborazioni ed associazioni con dati detenuti da terzi, permettere di identificare gli utenti. In questa categoria di dati rientrano gli indirizzi IP o i nomi a dominio dei computer utilizzati dagli utenti che si connettono al sito, gli indirizzi in notazione URI (Uniform Resource Identifier) delle risorse richieste, l'orario della richiesta, il metodo utilizzato nel sottoporre la richiesta al server, la dimensione del file ottenuto in risposta, il codice numerico indicante lo stato della risposta data dal server (buon fine, errore ...) ed altri parametri relativi al sistema operativo e all'ambiente informatico dell'utente. Questi dati vengono utilizzati al solo fine di ricavare informazioni statistiche anonime sull'uso del sito e per controllarne il corretto funzionamento e vengono cancellati immediatamente dopo l'elaborazione. I dati potrebbero essere utilizzati per l'accertamento di responsabilità in caso di ipotetici reati informatici ai danni del sito.</p>

            <strong>Cookies e localStorage</strong>
            <p>Alcune funzionalità di questo sito potrebbero fare uso di piccole quantità di dati salvati in cookies o localStorage. Si tratta di dati del tutto anonimi, utili a scopo tecnico o statistico, e nessuno di questi dati verrà utilizzato per identificarti o per contattarti. Per saperne di più su cookies e localStorage, visita il sito <a target='_blank' href='http://www.garanteprivacy.it/web/guest/home/docweb/-/docweb-display/docweb/3118884'>Garante per la protezione dei dati personali</a>.</p>
            <p>Per abilitare o disabilitare tutti i cookies mediante la configurazione del browser, clicca sul collegamento corrispondente e segui le istruzioni indicate:</p>

            <p>
                <a target='_blank' href='https://support.google.com/accounts/answer/61416?hl=it'><span>Google Chrome</span></a>
                <a target='_blank' href='https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie'><span>Mozilla Firefox</span></a>
                <a target='_blank' href='http://windows.microsoft.com/it-it/internet-explorer/delete-manage-cookies'><span>Microsoft Internet Explorer</span></a>
                <a target='_blank' href='http://help.opera.com/Windows/10.00/it/cookies.html'><span>Opera</span></a>
                <a target='_blank' href='https://support.apple.com/kb/PH17191?viewlocale=it_IT&locale=it_IT'><span>Safari</span></a>
            </p>

            <strong>Elenco dei cookies attualmente in uso:</strong>
            <p id='cookies-list'>Nessun cookie è attualmente in uso.</p>

            <strong>Servizi di terze parti che potrebbero essere utilizzati in questo sito e che potrebbero utilizzare cookies:</strong>
            <ul>
                <li style='display:none;'><a href='http://whos.amung.us/legal/privacy/'>whos.amung.us</a></li>
                <li><a href='http://www.addthis.com/privacy/privacy-policy'>Addthis</a> (<a href='http://www.addthis.com/privacy/opt-out'>Opt-out</a>)</li>
                <li><a href='https://www.tawk.to/privacy-policy/'>Tawk.to</a></li>

                <li style='display:none;'><a href='https://www.facebook.com/privacy/explanation'>Facebook</a></li>
                <li style='display:none;'><a href='https://www.linkedin.com/legal/privacy-policy'>Linkedin</a></li>
                <li style='display:none;'><a href='http://twitter.com/privacy'>Twitter</a></li>
                <li style='display:none;'><a href='https://vimeo.com/cookie_policy'>Vimeo</a></li>
                <li style='display:none;'><a href='http://www.dailymotion.com/legal/cookiespolicy'>Dailymotion</a></li>
                <li style='display:none;'><a href='https://help.disqus.com/customer/portal/topics/215159-terms-and-policies/articles'>Disqus</a></li>

                <li><a href='http://www.google.com/intl/it_it/policies/technologies/cookies/'>Servizi Google</a> (<a href='https://www.google.com/settings/ads/plugin'>Opt out</a>)</li>
                <li style='display:none;'><a href='http://www.google.com/intl/it_it/policies/technologies/cookies/'>Google Analytics</a> (<a href='https://www.google.com/settings/ads/plugin'>Opt out</a>)</li>
                <li style='display:none;'><a href='http://www.google.com/intl/it_it/policies/technologies/cookies/'>Google Adsense</a> (<a href='https://www.google.com/settings/ads/plugin'>Opt out</a>)</li>
                <li style='display:none;'><a href='http://www.google.com/intl/it/policies/technologies/cookies/'>Google Maps</a> (<a href='https://www.google.com/settings/ads/plugin'>Opt out</a>)</li>
                <li style='display:none;'><a href='http://www.google.com/intl/it/policies/technologies/cookies/'>Youtube</a> (<a href='https://www.google.com/settings/ads/plugin'>Opt out</a>)</li>

                <li style='display:none;'><a href='http://www.jsdelivr.com/'>jsDelivr</a></li>
            </ul>

            <p>Se l'utente desidera avere informazioni relative ai suddetti servizi o desidera disabilitarli definitivamente (anche al di fuori di questo sito) è possibile farlo accedendo alla pagina <a href='http://www.youronlinechoices.com/it/le-tue-scelte'>YourOnlineChoices.com</a> (in italiano) o alla pagina <a href='http://www.aboutads.info/choices/'>AboutAds.com</a> (in inglese).</p>

            <strong>Facoltatività del conferimento dei dati</strong>
            <p>A parte quanto specificato per i dati di navigazione, l'utente è libero di fornire i dati personali per richiedere i servizi offerti. Il loro mancato conferimento può comportare l'impossibilità di ottenere il servizio richiesto.</p>


            <strong>Modalità di trattamento dei dati</strong>
            <p>I dati personali sono trattati con strumenti informatici automatizzati, per il tempo necessario a conseguire lo scopo per il quale sono stati raccolti. Specifiche misure di sicurezza sono osservate per prevenire la perdita dei dati, usi illeciti o non corretti ed accessi non autorizzati.</p>


            <strong>Diritti degli interessati</strong>
            <p>I soggetti cui si riferiscono i dati hanno i diritti previsti dall'art. 7 del Codice privacy che prevede, tra gli altri il diritto di conoscere la presenza dei suoi dati e di ottenere la cancellazione, la rettifica, l'aggiornamento dei dati stessi rivolgendosi al Titolare del trattamento all'indirizzo sopra indicato. </p>
        </div>
    </div>


    <footer class="page-footer blue darken-1">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-text">cookieBAR</h5>
                    <p class="grey-text text-lighten-4">
                        I hope that you will try and enjoy cookieBAR as much as I did writing it. If so, please drop me a line at <a class="grey-text text-lighten-1" href="http://emanuele.itoscano.com/nodes/view/63_contacts">My website</a>. If cookieBAR has been really useful to you, please consider to make a small donation as a token of your appreciation and to help me to keep this up :)

                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
                        ">
                        <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
                        <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
                        </form>
                    </p>
                </div>
                <div class="col l4 offset-l2 s12">
                    <h5 class="white-text">Links</h5>
                    <ul>
                        <li><a class="grey-text text-lighten-3" href="https://github.com/ToX82/cookie-bar">GitHub repository</a></li>
                        <li><a class="grey-text text-lighten-3" href="https://www.linkedin.com/in/emanueletoscano/en">My Linkedin page</a></li>
                        <li><a class="grey-text text-lighten-3" href="http://emanuele.itoscano.com">Random stuff on my website</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                © <?= date("Y")?> Emanuele "ToX" Toscano

                - <a class="grey-text text-lighten-3" href='./'>Home page</a>

                <!--
                <span class='counter'>
                    <script id="_wau7fo">var _wau = _wau || []; _wau.push(["small", "ryzhklk2fz5i", "7fo"]);
                    (function() {var s=document.createElement("script"); s.async=true;
                    s.src="http://widgets.amung.us/small.js";
                    document.getElementsByTagName("head")[0].appendChild(s);
                    })();</script>
                </span>
                -->

                <span class="right">P.IVA 03218180044</span>
            </div>
        </div>
    </footer>


<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.95.3/js/materialize.min.js"></script>
<script src="//cdn.jsdelivr.net/prism/0.1/prism.full.min.js"></script>

<?php if ($_COOKIE['cookiebar'] == "CookieAllowed") { ?>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5571f1ac18f4dec2" async="async"></script>

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/56dd3faefd8c93706673ec32/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
<?php } ?>

<script src="cookiebar-latest.js"></script>
</body>
</html>
