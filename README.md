# 🛥️ BoatData Vessel Tracker

An interactive web map interface for visualizing vessel movement using GPS tracking data, built for [BoatData UK](https://boatdata.co.uk/). This system allows marine data visualization based on IoT tracking, enabling users to view boat paths by day and interact with the movement history via colored lines and popups.

---

## 🌐 About the Client: BoatData UK

[BoatData UK](https://boatdata.co.uk/) specializes in providing marine GPS data loggers and boat tracking systems. Their services are built for leisure boaters, professionals, and organizations requiring historical movement data of watercraft. With a user-friendly web dashboard, BoatData enables secure access and visualization of boat journeys across global waters. This project is a custom mapping frontend that integrates directly with BoatData's tracking system to visualize vessel movement in a clean, intuitive interface.

---

## ✨ Key Features

- 🗺️ **Interactive Web Map:** Uses Leaflet.js and OpenSeaMap tiles.
- 🌈 **Color-Coded Vessel Tracks:** Each day's track is shown in a unique color.
- 📍 **Clickable Lines:** Users can click on lines to reveal detailed popups with the MAC address and the date of travel.
- 📆 **Grouped by Date:** GPS data points are grouped and styled per day.
- ⚡ **Optimized Queries:** Server filters and aggregates data for high performance.
- 💡 **Responsive Interface:** Works across desktops, tablets, and mobile browsers.

---

## 🧰 Built With

- **Frontend:** HTML, CSS, JavaScript (Leaflet.js)
- **Backend:** PHP
- **Database:** MySQL
- **Libraries:**  
  - [Leaflet](https://leafletjs.com/) – Mapping engine  
  - [OpenSeaMap](https://wiki.openseamap.org/wiki/OpenSeaMap_tiles) – Nautical tile overlays  
  - [RandomColor PHP](https://github.com/davidmerfield/randomColor) – Random bright colors for each day's track

---

## 📁 Project Structure

- project-root/
├── index.php # Main file for rendering the interactive map
├── randomcolor.php # PHP library for generating bright colors
├── sql_include_new.php # MySQL connection configuration
├── README.md # This documentation


---

## ⚙️ How to Use

1. **Clone the Repository:**

```bash
git clone https://github.com/yourusername/boatdata-tracker.git
cd boatdata-tracker

2. **Configure Database**
Edit sql_include_new.php to match your MySQL configuration:

$sql_hostname = "localhost";
$sql_username = "your_username";
$sql_password = "your_password";
$sql_data_table = "your_database";

Ensure your boatdata table contains columns:

mac (MAC address)

latdec, londec (latitude & longitude)

date, utc, datetime, val (status, time, and value fields)

3. **Run Locally (Optional)**

```bash
Serve with a local PHP server:
php -S localhost:8000
