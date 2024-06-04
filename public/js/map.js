// Initialize and add the map
let map;

async function initMap() {

  // On récupère les données stocké dans l'attribut datadataProjects de la div "map"
  var dataProjects = document.getElementById("map");
  let p = dataProjects.dataset.projects

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
  let markers = [];


  JSON.parse(p).forEach(element => {

    // Si le projet possède une date de fin
    if (element.endDate) {

      // On récupère la date de fin en formation php
      var myDate = element.endDate;

      // On utilise split pour formaté la date afin de pouvoir l'utilisé coté javascript
      myDate = myDate.split("T");

      // On instancie une nouvelle date en format timestamp ( on ajoute une journée pour include les projet du jour actuel )
      var newDate = new Date(myDate[0]).getTime() + (86400*1000)
        
      // on compare avec la date actuelle
      if (newDate >=  new Date().getTime() )
      { 
        // Si la date de fin est plus grande ou egale avec celle actuelle alors on affiche le marqueur
        markers.push([{ lat: element.latitude, lng: element.longitude }, element.id, element.image, element.title, element.description, element.email, element.phone, element.category.image, element.startDate, element.endDate]);
      }

    } else {
      // Si la date de fin n'ai pas renseigné alors on affiche directement le marqueur
      markers.push([{ lat: element.latitude, lng: element.longitude }, element.id, element.image, element.title, element.description, element.email, element.phone, element.category.image, element.startDate, element.endDate]);
    }

  });

  console.log(markers)

  // On boucle sur le tableaux markers pour placer nos repère sur la carte
  markers.forEach(([position, id, image, title, description, email, phone, category_image, start_date, end_date], i) => {
    // On créé le repère
    const marker = new google.maps.Marker({
      position: position,
      map,
      icon: "/img/upload/markers/"+category_image,
      title: title,
    });

      // On utilise split pour formaté la date de début
      start_date = start_date.split("T");
      start_date = start_date[0].split("-");
      start_date = start_date[2]+'/'+start_date[1]+'/'+start_date[0]

      if (end_date) {
      // On utilise split pour formaté la date de fin
      end_date = end_date.split("T");
      end_date = end_date[0].split("-");
      end_date = end_date[2]+'/'+end_date[1]+'/'+end_date[0]
      }
      
    // Affichage de l'infobull
    const contentString =
      '<div id="content">' +
      '<h3 id="firstHeading" class="mb-3">'+title+'</h3>' +
      "<p>Début : "+start_date + (end_date ? " | Fin : "+end_date : "" )+"</p>" +
      '<div id="bodyContent">' +
      "<h5>Description du projet</h5>" +
      "<p>"+description+"</p>" +
      "<h5>Contact</h5>" +
      '<p>Adresse email: '+email+' </p>' +
      (phone ? '<p>Numéro de téléphone: '+phone+' </p>' : "") +
      "<p class='text-center'><a class='btn btn-success' href='/visitor/project/"+id+"/show'>Voir la page du projet</a></p>" +
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