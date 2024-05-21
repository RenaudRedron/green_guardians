// Initialize and add the map
let map;

async function initMap() {

  // On importe l'objet Map de google.maps
  const { Map } = await google.maps.importLibrary("maps");

  // La carte centré sur le centre de la France
  map = new Map(document.getElementById("map"), {
    zoom: 6,
    center: { lat: 46.7667, lng: 2.45 }, // Position de centrage de la carte
    zoomControl: true, // On autorise le zoom
    mapTypeControl: false,
    streetViewControl: false, // On n'autorise pas la street view
    fullscreenControl: false, // On n'autorise pas le plein écran
    mapId: "569f51b37acffc57", // Id de la carte personnalisé
  });

  // Tableaux contenant toute les coordonnées des répères indique aussi le title et le type
  const markers = [
    [{ lat: 46.7667, lng: 2.20 }, "Boynton Pass",1],
    [{ lat: 46.7667, lng: 2.45 }, "Airport Mesa",1],
    [{ lat: 46.854, lng: 1.45 }, "Chapel of the Holy Cross",0],
    [{ lat: 46.7667, lng: 2.35 }, "Red Rock Crossing",0],
    [{ lat: 46.7667, lng: 2.01 }, "Bell Rock",1],
  ];

  // On boucle sur le tableaux markers pour placer nos repère sur la carte
  markers.forEach(([position, title, type], i) => {
    
    // On choisi l'image à afficher selon le type du repère
    let image = ''
    if ( type == 0 ) {
      image = "/img/pointeur-arbre.png";
    } else if ( type == 1 ) {
      image = "/img/pointeur-dechet.png";
    } else {}

    // On crai le repère
    const marker = new google.maps.Marker({
      position: position,
      map,
      icon: image,
      title: title,
    });

    // Affichage de l'infobull
    const contentString =
      '<div id="content">' +
      '<div id="siteNotice">' +
      "</div>" +
      '<h1 id="firstHeading" class="firstHeading">Uluru</h1>' +
      '<div id="bodyContent">' +
      "<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large " +
      "sandstone rock formation in the southern part of the " +
      "Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) " +
      "south west of the nearest large town, Alice Springs; 450&#160;km " +
      "(280&#160;mi) by road. Kata Tjuta and Uluru are the two major " +
      "features of the Uluru - Kata Tjuta National Park. Uluru is " +
      "sacred to the Pitjantjatjara and Yankunytjatjara, the " +
      "Aboriginal people of the area. It has many springs, waterholes, " +
      "rock caves and ancient paintings. Uluru is listed as a World " +
      "Heritage Site.</p>" +
      '<p>Attribution: Uluru, <a href="https://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">' +
      "https://en.wikipedia.org/w/index.php?title=Uluru</a> " +
      "(last visited June 22, 2009).</p>" +
      "</div>" +
      "</div>";

    // On crai l'infobull
    const infowindow = new google.maps.InfoWindow({
      content: contentString,
      ariaLabel: "ariaLabel",
    });

    // On ajout un nouvelle évènement click sur notre repère
    marker.addListener("click", () => {
      infowindow.open({
        anchor: marker,
        map,
      });
    });
  })
  }

// Execution de la fonction
initMap();