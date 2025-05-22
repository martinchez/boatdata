<?php 
include 'randomcolor.php';
use \Colors\RandomColor;
?>
<!DOCTYPE html>
<html lang="en">
<?php
require_once "sql_include_new.php";
global $sql_hostname, $sql_username, $sql_password, $sql_data_table;

if (isset($_GET["uid"])) {
    $uid = $_GET["uid"];

    $mysqli = new mysqli($sql_hostname, $sql_username, $sql_password, $sql_data_table);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
} else {
    die("ERROR: 10: Need mac address");
}

// Query data grouped by date
$data = mysqli_query($mysqli, "
    SELECT latdec, londec, DATE(date) as day 
    FROM boatdata 
    WHERE mac = '$uid' 
      AND val = 'A'  
      AND utc != '00:00:00' 
      AND utc < '24:00:00' 
      AND MINUTE(boatdata.utc) % 3 = 0  
    GROUP BY day, HOUR(boatdata.utc), MINUTE(boatdata.utc) 
    ORDER BY boatdata.datetime ASC 
    LIMIT 25000
");

// Group coordinates by day
$groupedCoordinates = [];
while ($info = mysqli_fetch_assoc($data)) {
    $day = $info['day'];
    $groupedCoordinates[$day][] = [(float)$info['londec'], (float)$info['latdec']];
}

// Generate random colors per day
$days = array_keys($groupedCoordinates);
$colors = RandomColor::many(count($days), ['luminosity' => 'bright']);

// Build GeoJSON Features per day
$features = [];
foreach ($days as $index => $day) {
    $coordinates = $groupedCoordinates[$day];
    if (count($coordinates) < 2) continue; // skip if not enough points

    $features[] = [
        "type" => "Feature",
        "geometry" => [
            "type" => "LineString",
            "coordinates" => $coordinates
        ],
        "properties" => [
            "date" => $day,
            "color" => $colors[$index],
            "mac" => $uid
        ]
    ];
}

$geojson = [
    "type" => "FeatureCollection",
    "features" => $features
];
?>

<head>
    <meta charset="utf-8">
    <title>Boat Tracking Viewer | BoatData.co.uk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
            background-color: #f2f7f9;
        }

        #map {
            height: 80vh;
            width: 100%;
        }

        .header {
            background-color: #003366;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .description {
            max-width: 900px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .description h2 {
            color: #003366;
            margin-top: 0;
        }

        .description p {
            font-size: 16px;
            color: #444;
            line-height: 1.6;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>BoatData Marine Vessel Tracker</h1>
    </div>

    <div class="description">
        <h2>Project Overview</h2>
        <p>
            This client project is built for <a href="https://boatdata.co.uk/" target="_blank">BoatData UK</a>, a company specializing in real-time vessel data monitoring and tracking solutions.
            <br><br>
            This web map interface enables dynamic visualization of vessel movement using data collected via onboard tracking devices. 
            Each vessel track is grouped by day and color-coded for clarity. Users can interactively click on the marine tracks to 
            see detailed metadata including the vessel's MAC address and date of the recorded movement.
            <br><br>
            The tool supports efficient spatial-temporal vessel data analysis with optimized server-side queries and smart client-side rendering using Leaflet.
        </p>
    </div>

    <div id="map"></div>

    <script>
        const geojsonData = <?= json_encode($geojson, JSON_NUMERIC_CHECK); ?>;

        const map = L.map('map');

        const osmLayer = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            minZoom: 4,
            maxZoom: 20,
            attribution: 'Map data © OpenStreetMap contributors'
        });

        const seaLayer = L.tileLayer('https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png', {
            minZoom: 4,
            maxZoom: 20
        });

        map.addLayer(osmLayer);
        map.addLayer(seaLayer);

        const features = L.geoJSON(geojsonData, {
            style: feature => ({
                color: feature.properties.color || "#ff007b",
                weight: 2,
                opacity: 0.85
            }),
            onEachFeature: (feature, layer) => {
                const popupContent = `
                    <strong>Vessel MAC:</strong> ${feature.properties.mac}<br>
                    <strong>Date:</strong> ${feature.properties.date}
                `;
                layer.bindPopup(popupContent);
            }
        }).addTo(map);

        map.fitBounds(features.getBounds(), { padding: [5, 5] });
    </script>

</body>
</html>
