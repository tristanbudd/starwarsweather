<?php
// Check if a cookie named 'ip_address' is set, which stores the user's IP address.
if (!isset($_COOKIE['ip_address'])) {
    // If the cookie is not set, try to get the user's IP address from various server headers.
    // If none of these headers are set, display an error message and exit.
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
        // Display an error message and exit if no IP address is found.
        echo("<script>alert('Error found loading geoip data.'</script>)");
        exit();
    }

    // Set a cookie named 'ip_address' with the user's IP address, which expires in 30 days.
    setcookie("ip_address", $ip, time() + (86400 * 30), "/");
} else {
    // If the cookie is already set, retrieve the user's IP address from the cookie.
    $ip = $_COOKIE['ip_address'];
}

// Define the URL for the location data API (in this case, it's ip-api.com) based on the user's IP address.
$locationApiUrl = "http://ip-api.com/json/" . $ip;

// Fetch location data from the API using file_get_contents.
$locationData = file_get_contents($locationApiUrl);

// Check if the location data could not be fetched. If so, display an error message and exit.
if ($locationData === false) {
    echo("<script>alert('Error fetching location data')</script>");
    exit();
}

// Parse the location data as JSON.
$location = json_decode($locationData);

// Check if the JSON parsing was successful and if latitude and longitude are available in the data.
if ($location === null || !isset($location->lat) || !isset($location->lon)) {
    echo("<script>alert('Error parsing location data')</script>");
    exit();
}

// Extract latitude and longitude from the location data.
$latitude = $location->lat;
$longitude = $location->lon;

// Replace "[Your openweathermap API Key]" with your actual OpenWeatherMap API key.
$apiKey = "[Your openweathermap API Key]";

// Define the URL for the weather data API (OpenWeatherMap) based on the user's latitude, longitude, and your API key.
$apiUrl = "http://api.openweathermap.org/data/2.5/weather?lat=" . $latitude . "&lon=" . $longitude . "&lang=en&units=metric&appid=" . $apiKey;

// Initialize a cURL session to make an HTTP request to the weather data API.
$ch = curl_init();

// Set cURL options for the HTTP request.
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Execute the cURL request and store the response.
$response = curl_exec($ch);

// Close the cURL session.
curl_close($ch);

// Check if the response from the API is false, indicating an error.
if ($response === false) {
    echo("<script>alert('Error fetching weather data')</script>");
    exit();
}

// Parse the weather data JSON.
$weather = json_decode($response);

// Check if the JSON parsing was successful and if required weather data is available.
if ($weather === null || !isset($weather->main) || !isset($weather->weather[0])) {
    echo("<script>alert('Error parsing weather data')</script>");
    exit();
}

// Extract weather information from the JSON.
$temperature = $weather->main->temp;
$temperature_min = $weather->main->temp_min;
$temperature_max = $weather->main->temp_max;
$humidity = $weather->main->humidity;
$clouds_description = ucfirst($weather->weather[0]->description);
$clouds_icon = "https://openweathermap.org/img/wn/" . $weather->weather[0]->icon . "@2x.png";

// Determine the Star Wars-themed content based on the temperature range.
if ($temperature < -5) {
    // Display content for "Hoth".
    $title = "Feels like: Hoth";
    $description = "Hoth is the sixth planet in the remote system of the same name,<br> and was the site of the Rebel Alliance's Echo Base. It is a<br> world of snow and ice, surrounded by numerous moons, and home<br> to deadly creatures like the wampa.";
    $image_url = "https://wallpaperaccess.com/full/2811722.jpg";
    $image_alt = "An image of the planet Hoth from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= -5 and $temperature < 5) {
    // Display content for "Mygeeto".
    $title = "Feels like: Mygeeto";
    $description = "A frigid Outer Rim planet marked by jagged crystalline landscapes,<br> Mygeeto was the homeworld of the Lurmen and controlled by the<br> InterGalactic Banking Clan. During the Outer Rim Sieges,<br> Ki-Adi-Mundi and clone forces under Commander Bacara battled<br> Separatist droids for control of Mygeeto. When Supreme Chancellor<br> Palpatine issued Order 66, Bacara and his clonesshot their <br>Jedi General dead.";
    $image_url = "https://media.moddb.com/images/mods/1/20/19631/20210627023302_1.jpg";
    $image_alt = "An image of the planet Mygeeto from the Star Wars Galaxy.";
    $image_credit = "moddb.com";
    $image_credit_link = "https://www.moddb.com/mods/the-mass-effect-mod/images/mygeeto-lighting-overhaul";
} elseif ($temperature >= 5 and $temperature < 10) {
    // Display content for "Endor".
    $title = "Feels like: Endor";
    $description = "Secluded in a remote corner of the galaxy, the forest moon of Endor<br> would easily have been overlooked by history were it not for<br> the decisive battle that occurred there. The lush, forest<br> home of the Ewok species is the gravesite of Darth Vader and<br> the Empire itself. It was here that the Rebel Alliance won its most<br> crucial victory over the Galactic Empire.";
    $image_url = "https://wallpaperaccess.com/full/4341124.jpg";
    $image_alt = "An image of the planet Endor from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 10 and $temperature < 15) {
    // Display content for "Dagobah".
    $title = "Feels like: Dagobah";
    $description = "Home to Yoda during his final years, Dagobah was a swamp-covered planet<br> strong with the Force -- a forgotten world where the wizened,<br> Jedi Master could escape the notice of Imperial forces.<br> Characterized by its bog-like conditions and fetid wetlands,<br> the murky and humid quagmire was undeveloped, with no signs of technology.<br> Though it lacked civilization, the planet was teeming<br> with life -- from its dense, jungle undergrowth to its diverse animal population.<br> Home to a number of fairly common reptilian and amphibious creatures,<br> Dagobah also boasted an indigenous population of much more massive -- and mysterious<br> -- lifeforms. Surrounded by creatures generating the living Force,<br> Yoda learned to connect with the deeper cosmic Force and waited for<br> one who might bring about the return of the Jedi Order.";
    $image_url = "https://wallpaperaccess.com/full/9447065.jpg";
    $image_alt = "An image of the planet Dagobah from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 15 and $temperature < 20) {
    // Display content for "Coruscant".
    $title = "Feels like: Coruscant";
    $description = "Coruscant is the vibrant heart and capital of the galaxy during the age<br> of the Empire, featuring a diverse mix of cultures and citizens<br> spread over hundreds of levels. Once the home of the<br> main Jedi Temple -- the central hub of Jedi training and learning<br> for over a thousand generations and the repository of the Jedi Archives<br> -- these traditions ended when the planet bore witness to Order 66.";
    $image_url = "https://wallpaperaccess.com/full/2418884.jpg";
    $image_alt = "An image of the planet Coruscant from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 20 and $temperature < 25) {
    // Display content for "Naboo".
    $title = "Feels like: Naboo";
    $description = "An idyllic world close to the border of the Outer Rim Territories,<br> Naboo is inhabited by peaceful humans known as the Naboo, and an<br> indigenous species of intelligent amphibians called the Gungans.<br> Naboo's surface consists of swampy lakes, rolling plains and<br> green hills. Its population centers are beautiful -- Naboo's river<br> cities are filled with classical architecture and greenery, while the<br> underwater Gungan settlements are a beautiful display of exotic<br> hydrostatic bubble technology.";
    $image_url = "https://wallpaperaccess.com/full/4540612.jpg";
    $image_alt = "An image of the planet Naboo from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 25 and $temperature < 30) {
    // Display content for "Kashyyyk".
    $title = "Feels like: Kashyyyk";
    $description = "Kashyyyk is the Wookiee homeworld, covered in dense forest. While<br> Wookiees build their homes in the planet's trees, they are not a<br> primitive species, and Kashyyyk architecture incorporates sophisticated<br> technology. One of the last battles of the Clone Wars<br> was fought here under the leadership of Yoda, with Wookiees and clones<br> battling the Separatist droid army -- until the Emperor issued<br> Order 66, commanding the clones to slaughter all Jedi. Yoda survived, <br>however, with the help of natives Chewbacca and Tarfful, <br>who used a hidden shuttle to evacuate the Jedi Master from the planet.<br> In the aftermath, the brave Jedi Padawan Gungi, a survivor of the<br> Jedi slaughter, was returned to what remained of his homeworld<br> with the help of Clone Force 99.";
    $image_url = "https://wallpaperaccess.com/full/5533313.jpg";
    $image_alt = "An image of the planet Kashyyyk from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 30 and $temperature < 35) {
    // Display content for "Tatooine."
    $title = "Feels like: Tatooine";
    $description = "Tatooine is harsh desert world orbiting twin suns in the galaxy’s<br> Outer Rim. In the days of the Empire and the Republic, many settlers<br> scratched out a living on moisture farms, while spaceport cities such<br> as Mos Eisley and Mos Espa served as home base for smugglers,<br> criminals, and other rogues. Anakin Skywalker and Luke Skywalker<br> both once called Tatooine home, although across the stars it<br> was more widely known as a hive of scum and villainy ruled by the crime<br> boss Jabba the Hutt.";
    $image_url = "https://wallpaperaccess.com/full/1251069.jpg";
    $image_alt = "An image of the planet Tatooine from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} elseif ($temperature >= 35) {
    // Display content for "Mustafar".
    $title = "Feels like: Mustafar";
    $description = "A tiny, fiery planet in the Outer Rim, Mustafar maintains an erratic<br> orbit between two gas giants. Mustafar is rich in unique and valuable<br> minerals which have long been mined by the Tech Union. Its lava pits<br> and rivers make this planet a dangerous habitat; its natives<br> have burly, tough bodies that can withstand extreme heat. The<br> planet became the backdrop for the fateful duel between<br> Obi-Wan Kenobi and Anakin Skywalker. After the rise of the Empire, captured Jedi<br> were brought to the volcanic world for interrogation and execution..";
    $image_url = "https://wallpaperaccess.com/full/4780198.jpg";
    $image_alt = "An image of the planet Mustafar from the Star Wars Galaxy.";
    $image_credit = "wallpaperaccess.com";
    $image_credit_link = "https://wallpaperaccess.com";
} else {
    // If something goes wrong, end the script.
    echo("<script>alert('Error loading planet data')</script>");
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

    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
    <link rel="manifest" href="/icons/site.webmanifest">
    <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#0e0e0e">
    <link rel="shortcut icon" href="/icons/favicon.ico">
    <meta name="msapplication-TileColor" content="#0e0e0e">
    <meta name="msapplication-TileImage" content="/icons/mstile-144x144.png">
    <meta name="msapplication-config" content="/icons/browserconfig.xml">
    <meta name="theme-color" content="#0e0e0e">

    <link rel="stylesheet" href="style.css">
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
