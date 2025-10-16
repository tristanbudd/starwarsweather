# StarWars Weather
![](https://img.shields.io/github/stars/tristanbudd/starwarsweather.svg) ![](https://img.shields.io/github/forks/tristanbudd/starwarsweather.svg) ![](https://img.shields.io/github/issues/tristanbudd/starwarsweather.svg)

## Project Description
This was a simple passion project a started a while ago, It first gets your location and then locally compares it to the GeoLite database to get a city name. From this city name it utilises the OpenWeatherAPI to get weather information and shows what star wars planet best fits your local temperature!

> [!WARNING]
> Currently not responsive for mobile devices. (Desktop Only)

---

## Preview Images
Below are examples showcasing different sections of the website:

### Local Preview
<img width="1920" height="945" alt="Local Preview" src="https://github.com/user-attachments/assets/2e5708f9-54dc-49be-81fe-51ec1960b3e0" />

### Various Locations (Using GeoPeeker)
<img width="1285" height="714" alt="Various Locations" src="https://github.com/user-attachments/assets/db738ee6-fbcf-4b5c-8d24-cd8a9aab3228" />

---

## Installation / Setup

1. **Clone the repository.**

2. **In .env or on Vercel under Environmental Variables**:
Follow .env.example and set OPENWEATHER_API_KEY to your API key.
   
4. **Ensure your web server is running**:
    - Apache with PHP enabled

5. **Should be good to go!**

---

## License

[MIT](LICENSE)
