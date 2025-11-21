<?php
require __DIR__ . "/php/connection.php";
require __DIR__ . "/php/helpers.php";

session_start();

// Session Check
if (empty($_SESSION["username"])) {
    flash_redirect("./login.html", "Bitte zuerst einloggen", false, "login");
}

$username = htmlspecialchars($_SESSION["username"]);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Farm-Idle <?= $username ?></title>
    <link rel="icon" type="image/sproud-icon" href="./img/svg/sprout.png">

    <link rel="stylesheet" href="./style/style.css">
    <link rel="icon" href="data:,">

    <script defer src="./js/config.js"></script>
    <script defer src="./js/storage.js"></script>
    <script defer src="./js/process.js"></script>
    <script defer src="./js/markt.js"></script>
    <script defer src="./js/base.js"></script>
    <script defer src="./js/game.js"></script>
    <script defer src="./js/inventory.js"></script>
    <script defer src="./js/farm.js"></script>

</head>

<body>
    <div class="layout">
        <!-- Seitenbar -->
        <aside class="sidebar">
            <strong>Farm Idle</strong>
            <ul class="nav">
                <li><a href="#" data-tab="tab-farm" class="active"><img class="icon"
                            src="./img/svg/building-cottage.svg" alt="Farm" />Farm</a></li>
                <li><a href="#" data-tab="tab-process"><img class="icon" src="./img/svg/settings.svg"
                            alt="Verarbeitung" />Verarbeitung</a></li>
                <li><a href="#" data-tab="tab-markt"><img class="icon" src="./img/svg/building-store.svg"
                            alt="Markt" />Markt</a></li>
                <li><a href="#" data-tab="tab-inventory"><img class="icon" src="./img/svg/building-warehouse.svg"
                            alt="Inventory" />Inventar</a></li>
            </ul>
        </aside>

        <!-- Hud Header -->
        <div class="content">
            <header class="topbar">
                <div class="hud"><span>Gold: <b id="hud-gold">0</b></span><span>Inventar: <b
                            id="hud-inventory">0</b></span>
                </div>
                <img src="./img/svg/sprout.png" alt="sproud" width="40px">
                <div class="hud">
                    <span class="user" id="hud-user" data-username="<?= $username ?>"> <?= $username ?> </span>
                    <button type="button" class="btn btn-ghost" id="btn-save"><b>Speichern</b></button>
                    <button type="button" class="btn btn-ghost" onclick="location.href='./php/logout.php'"><b>Ausloggen</b></button>
                </div>

            </header>

            <main>
                <!-- Inventory für Template -->
                <template id="tpl-inventory-item">
                    <article class="card" data-item-id="">
                        <h3 class="item-title" data-ref="itemName">Item</h3>
                        <div class="row">
                            <img class="icon" data-ref="icon" alt="" />
                            <span>Bestand: <b class="stock" data-ref="stock">0</b></span>
                        </div>
                    </article>
                </template>

                <!-- Template für Felder -->
                <template id="tpl-field">
                    <article class="card" data-field-id="">
                        <h3 data-ref="title">Field #X</h3>
                        <p class="muted" data-ref="status">Status</p>
                        <div class="row">
                            <img class="icon" data-ref="cropIcon" src="./img/svg/field.svg" alt="" />
                            <span data-ref="cropName">Saat</span>
                        </div>
                        <div class="row muted" data-ref="stats">Benötigte Zeit</div>
                        <div class="progress" data-value="0"></div>
                        <button class="btn" data-action="sow">Säen</button>
                    </article>
                </template>

                <!-- Template für Process Rezept -->
                <template id="tpl-recipe">
                    <article class="card" data-recipe-id="">
                        <h3 data-ref="title">Rezept</h3>
                        <p class="subtext" data-ref="subtitle">A → B (Xs)</p>
                        <div class="row">
                            <img class="icon" data-ref="outIcon" alt="" />
                            <span data-ref="outName">Output</span>
                        </div>
                        <div class="row">
                            <span>Kosten:</span>
                            <span data-ref="costAmount">0</span>
                            <img class="icon" data-ref="costIcon" alt="" />
                        </div>
                        <div class="row">
                            <span>Verfügbar:</span>
                            <span data-ref="available">0</span>
                            <img class="icon" data-ref="availIcon" alt="" />
                        </div>
                        <button class="btn" data-action="start">Start</button>
                    </article>
                </template>

                <!-- Template für Process Aktive Jobs -->
                <template id="tpl-job">
                    <article class="card" data-job-id="">
                        <div class="row">
                            <img class="icon" data-ref="prodIcon" alt="" />
                            <strong data-ref="prodName">Produkt</strong>
                            <span class="badge" data-ref="state">läuft</span>
                        </div>
                        <div class="progress" data-value="0"></div>
                        <div class="row spread">
                            <small data-ref="timeLeft">Noch 0 s</small>
                            <button class="btn btn-ghost" data-action="cancel">Abbrechen</button>
                        </div>
                    </article>
                </template>

                <!-- Template für Markt -->
                        <template id="tpl-market">
                            <article class="card" data-item-id="">
                                <h3 class="item-title" data-ref="itemName">Item</h3>
                                <p class="subtext price">Preis/Stück: <span data-ref="price"></span> G</p>
                                <div class="row">
                                    <img class="icon" data-ref="icon" alt="" />
                                    <span>Bestand <b class="stock" data-ref="stock">0</b></span>
                                </div>
                                <div class="stack">
                                    <label class="row">Menge:
                                        <input class="qty" type="number" value="1" min="1" step="1" data-ref="qty" />
                                    </label>
                                    <button type="button" class="btn" data-action="sell">Verkaufen</button>
                                </div>
                            </article>
                        </template>


                <!-- Farm-Tab -->
                <section id="tab-farm" data-tab="tab-farm" class="is-active">
                    <h2 class="tab-title">Farm</h2>

                    <!-- Saat Tab -->
                    <nav class="toolbar">
                        <button type="button" class="btn btn-ghost" data-crop="wheat">
                            <img class="icon" src="./img/Crops/Farming-Wheat.png" alt="Weizen" />
                            <b>Weizen</b></button>
                        <button type="button" class="btn btn-ghost" data-crop="corn">
                            <img class="icon" src="./img/Crops/Vegetables-Cornpng.png" alt="Mais" />
                            <b>Mais</b></button>
                        <button type="button" class="btn btn-ghost" data-crop="soy">
                            <img class="icon" src="./img/Crops/Allergens-Soy-Bean.png" alt="Soja" />
                            <b>Soja</b></button>
                        <button id="btn-buy-field" type="button" class="btn btn-ghost">
                            <b>Feld Kaufen 100g</b></button>
                    </nav>

                    <!-- Felder Card -->
                    <div id="field-list" class="grid"> </div>

                </section>

                <!-- Verarbeitung -->
                <section id="tab-process" data-tab="tab-process" class="hidden">
                    <h2 class="tab-title">Verarbeitung</h2>

                    <!-- Rezepte Card -->
                    <div id="recipe-list" class="grid"> </div>

                    <!-- Aktive Jobs -->
                    <h2 class="tab-title jobs">Aktive Jobs <small>0/3</small></h2>
                    <div id="job-list" class="grid"> </div>

                </section>

                <!-- Markt Tab -->
                <section id="tab-markt" data-tab="tab-markt" class="hidden">
                    <h2 class="tab-title">Markt</h2>
                    <h3 class="tab-subtitle">Aktuelle Marktpreise </h3>

                    <!-- Markt Card -->
                    <div id="market-list" class="grid"> </div>
                </section>

                <!-- Inventar Tab -->
                <section id="tab-inventory" data-tab="tab-inventory">
                    <h2 class="tab-title">Inventar</h2>

                    <!-- Inventar Card -->
                    <div id="inv-list" class="grid"> </div>


                </section>
            </main>
        </div>
    </div>
</body>

</html>