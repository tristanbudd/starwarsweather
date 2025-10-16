<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php");

function get_document_path($path_type="", $component=false): string
{
    $is_running_locally = !(strpos($_SERVER["HTTP_HOST"], "localhost") === false);

    if ($is_running_locally) {
        $path = "../" . $path_type;
    } else {
        $path = "";

        if ($component) {
            $path = $_SERVER["DOCUMENT_ROOT"] . "/public";
        }
    }

    return $path;
}

use GeoIp2\Database\Reader;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED'];
} else if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
} else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_FORWARDED_FOR'];
} else if (isset($_SERVER['HTTP_FORWARDED'])) {
    $ip = $_SERVER['HTTP_FORWARDED'];
} else if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
} else {
    echo("<script>alert('Error found loading geoip data.'</script>)");
    exit();
}

try {
    $cityReader = new Reader(__DIR__ . '/db/GeoLite2-City.mmdb');

    $record = $cityReader->city($ip);

    $latitude = $record->location->latitude;
    $longitude = $record->location->longitude;
    $country = $record->country->name;
    $city = $record->city->name;
} catch (Exception $e) {
    echo("<script>alert('Error loading GeoLite2 data: " . addslashes($e->getMessage()) . "')</script>");
    exit();
}


$apiKey = getenv("OPENWEATHER_API_KEY");
$apiUrl = "http://api.openweathermap.org/data/2.5/weather?lat=" . $latitude . "&lon=" . $longitude . "&lang=en&units=metric&appid=" . $apiKey;
$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    echo("<script>alert('Error fetching weather data')</script>");
    exit();
}

$weather = json_decode($response);

if ($weather === null || !isset($weather->main) || !isset($weather->weather[0])) {
    echo("<script>alert('Error parsing weather data')</script>");
    exit();
}

$temperature = $weather->main->temp;
$temperature_min = $weather->main->temp_min;
$temperature_max = $weather->main->temp_max;
$humidity = $weather->main->humidity;
$clouds_description = ucfirst($weather->weather[0]->description);
$clouds_icon = "https://openweathermap.org/img/wn/" . $weather->weather[0]->icon . "@2x.png";

if ($temperature < -5) {
    $title = "Feels like: Hoth";
    $description = "Hoth is the sixth planet in the remote system of the same name,<br> and was the site of the Rebel Alliance's Echo Base. It is a<br> world of snow and ice, surrounded by numerous moons, and home<br> to deadly creatures like the wampa.";
    $image_url = "https://wallpaperaccess.com/full/2811722.jpg";
    $image_alt = "An image of the planet Hoth from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= -5 and $temperature < 5) {
    $title = "Feels like: Mygeeto";
    $description = "A frigid Outer Rim planet marked by jagged crystalline landscapes,<br> Mygeeto was the homeworld of the Lurmen and controlled by the<br> InterGalactic Banking Clan. During the Outer Rim Sieges,<br> Ki-Adi-Mundi and clone forces under Commander Bacara battled<br> Separatist droids for control of Mygeeto. When Supreme Chancellor<br> Palpatine issued Order 66, Bacara and his clonesshot their <br>Jedi General dead.";
    $image_url = "https://media.moddb.com/images/mods/1/20/19631/20210627023302_1.jpg";
    $image_alt = "An image of the planet Mygeeto from the Star Wars Galaxy.";
    $image_credit = "moddb.com";
    $image_credit_link = "https://www.moddb.com/mods/the-mass-effect-mod/images/mygeeto-lighting-overhaul";
} elseif ($temperature >= 5 and $temperature < 10) {
    $title = "Feels like: Endor";
    $description = "Secluded in a remote corner of the galaxy, the forest moon of Endor<br> would easily have been overlooked by history were it not for<br> the decisive battle that occurred there. The lush, forest<br> home of the Ewok species is the gravesite of Darth Vader and<br> the Empire itself. It was here that the Rebel Alliance won its most<br> crucial victory over the Galactic Empire.";
    $image_url = "https://wallpaperaccess.com/full/4341124.jpg";
    $image_alt = "An image of the planet Endor from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 10 and $temperature < 15) {
    $title = "Feels like: Dagobah";
    $description = "Home to Yoda during his final years, Dagobah was a swamp-covered planet<br> strong with the Force -- a forgotten world where the wizened,<br> Jedi Master could escape the notice of Imperial forces.<br> Characterized by its bog-like conditions and fetid wetlands,<br> the murky and humid quagmire was undeveloped, with no signs of technology.<br> Though it lacked civilization, the planet was teeming<br> with life -- from its dense, jungle undergrowth to its diverse animal population.<br> Home to a number of fairly common reptilian and amphibious creatures,<br> Dagobah also boasted an indigenous population of much more massive -- and mysterious<br> -- lifeforms. Surrounded by creatures generating the living Force,<br> Yoda learned to connect with the deeper cosmic Force and waited for<br> one who might bring about the return of the Jedi Order.";
    $image_url = "https://wallpaperaccess.com/full/9447065.jpg";
    $image_alt = "An image of the planet Dagobah from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 15 and $temperature < 20) {
    $title = "Feels like: Coruscant";
    $description = "Coruscant is the vibrant heart and capital of the galaxy during the age<br> of the Empire, featuring a diverse mix of cultures and citizens<br> spread over hundreds of levels. Once the home of the<br> main Jedi Temple -- the central hub of Jedi training and learning<br> for over a thousand generations and the repository of the Jedi Archives<br> -- these traditions ended when the planet bore witness to Order 66.";
    $image_url = "https://wallpaperaccess.com/full/2418884.jpg";
    $image_alt = "An image of the planet Coruscant from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 20 and $temperature < 25) {
    $title = "Feels like: Naboo";
    $description = "An idyllic world close to the border of the Outer Rim Territories,<br> Naboo is inhabited by peaceful humans known as the Naboo, and an<br> indigenous species of intelligent amphibians called the Gungans.<br> Naboo's surface consists of swampy lakes, rolling plains and<br> green hills. Its population centers are beautiful -- Naboo's river<br> cities are filled with classical architecture and greenery, while the<br> underwater Gungan settlements are a beautiful display of exotic<br> hydrostatic bubble technology.";
    $image_url = "https://wallpaperaccess.com/full/4540612.jpg";
    $image_alt = "An image of the planet Naboo from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 25 and $temperature < 30) {
    $title = "Feels like: Kashyyyk";
    $description = "Kashyyyk is the Wookiee homeworld, covered in dense forest. While<br> Wookiees build their homes in the planet's trees, they are not a<br> primitive species, and Kashyyyk architecture incorporates sophisticated<br> technology. One of the last battles of the Clone Wars<br> was fought here under the leadership of Yoda, with Wookiees and clones<br> battling the Separatist droid army -- until the Emperor issued<br> Order 66, commanding the clones to slaughter all Jedi. Yoda survived, <br>however, with the help of natives Chewbacca and Tarfful, <br>who used a hidden shuttle to evacuate the Jedi Master from the planet.<br> In the aftermath, the brave Jedi Padawan Gungi, a survivor of the<br> Jedi slaughter, was returned to what remained of his homeworld<br> with the help of Clone Force 99.";
    $image_url = "https://wallpaperaccess.com/full/5533313.jpg";
    $image_alt = "An image of the planet Kashyyyk from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 30 and $temperature < 35) {
    $title = "Feels like: Tatooine";
    $description = "Tatooine is harsh desert world orbiting twin suns in the galaxy’s<br> Outer Rim. In the days of the Empire and the Republic, many settlers<br> scratched out a living on moisture farms, while spaceport cities such<br> as Mos Eisley and Mos Espa served as home base for smugglers,<br> criminals, and other rogues. Anakin Skywalker and Luke Skywalker<br> both once called Tatooine home, although across the stars it<br> was more widely known as a hive of scum and villainy ruled by the crime<br> boss Jabba the Hutt.";
    $image_url = "https://wallpaperaccess.com/full/1251069.jpg";
    $image_alt = "An image of the planet Tatooine from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 35) {
    $title = "Feels like: Mustafar";
    $description = "A tiny, fiery planet in the Outer Rim, Mustafar maintains an erratic<br> orbit between two gas giants. Mustafar is rich in unique and valuable<br> minerals which have long been mined by the Tech Union. Its lava pits<br> and rivers make this planet a dangerous habitat; its natives<br> have burly, tough bodies that can withstand extreme heat. The<br> planet became the backdrop for the fateful duel between<br> Obi-Wan Kenobi and Anakin Skywalker. After the rise of the Empire, captured Jedi<br> were brought to the volcanic world for interrogation and execution..";
    $image_url = "https://wallpaperaccess.com/full/4780198.jpg";
    $image_alt = "An image of the planet Mustafar from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} else {
    echo("<script>alert('Error loading planet data.')</script>");
    exit();
}
?>

<!DOCTYPE html>
<head>
    <title>Star Wars Weather</title>
    <meta name="og:title" property="og:title" content="Star Wars Weather">
    <meta name="og:description" property="og:description" content="A simple use of some of my PHP skills to create a star wars themed temperature indicator with scenes and images from the star wars series.">
    <meta property="og:site_name" content="Star Wars Weather" />
    <meta property="og:type" content="website" />
    <meta name="author" content="https://github.com/tristanbudd">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">

    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo(get_document_path("public") . "/icons/apple-touch-icon.png"); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo(get_document_path("public") . "/icons/favicon-32x32.png"); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo(get_document_path("public") . "/icons/favicon-16x16.png"); ?>">
    <link rel="mask-icon" href="/public/icons/safari-pinned-tab.svg" color="#0e0e0e">
    <link rel="shortcut icon" href="<?php echo(get_document_path("public") . "/icons/favicon.ico"); ?>">
    <meta name="msapplication-TileColor" content="#0e0e0e">
    <meta name="msapplication-TileImage" content="<?php echo(get_document_path("public") . "/icons/mstile-144x144.png"); ?>">

    <link rel="stylesheet" href="<?php echo(get_document_path("public") . "/css/style.css"); ?>">
</head>
<body>
    <div class="main">
        <div class="vignette"></div>
        <div class="weather">
            <img src="<?php echo($clouds_icon) ?>" alt="Weather Icon">
            <h1 class="text"><?php echo($temperature) ?>°C</h1>
            <h2 class="text"><?php echo($clouds_description) ?></h2>
            <p class="text">Temperature Min: <?php echo($temperature_min) ?>°C<br>Temperature Max: <?php echo($temperature_max) ?>°C<br>Humidity: <?php echo($humidity) ?>%</p>
        </div>

        <div class="information">
            <h1 class="text"><?php echo($title) ?></h1>
            <p class="text"><?php echo($description) ?></p>
        </div>

        <img src="<?php echo($image_url) ?>" alt="<?php echo($image_alt) ?>">

        <div class="credit">
            <a class="text" href="<?php echo($image_credit_link) ?>">Image Credit: <?php echo($image_credit) ?></a>
        </div>
    </div>
</body>
