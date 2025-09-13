<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Téléchargement YouTube</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 600px;
      margin-top: 80px;
      padding: 30px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .btn-download {
      width: 100%;
    }
    #formats {
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="container text-center">
    <h2 class="mb-4">Téléchargez votre vidéo YouTube</h2>
    <p>Collez votre lien YouTube ci-dessous et choisissez le format :</p>
    
    <input type="text" id="youtubeUrl" class="form-control mb-3" placeholder="https://youtube.com/watch?v=...">
    
    <select id="format" class="form-select mb-3">
      <option value="audio">Audio MP3</option>
      <option value="video">Vidéo MP4</option>
    </select>
    
    <button id="downloadBtn" class="btn btn-primary btn-download">Télécharger</button>
    
    <div id="formats" class="text-start"></div>
  </div>

  <script>
    const apiKey = 'ytb-dw-8b8581065c96ca5e2d004b047b14ba34';

    document.getElementById('downloadBtn').addEventListener('click', () => {
      const youtubeUrl = document.getElementById('youtubeUrl').value.trim();
      const format = document.getElementById('format').value;

      if (!youtubeUrl) {
        alert("Veuillez entrer un lien YouTube.");
        return;
      }

      // Récupérer les infos de la vidéo
      fetch(`https://ytb-dw.social-networking.me/video_info.php?url=${encodeURIComponent(youtubeUrl)}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Afficher les infos dans la page
            const infoDiv = document.getElementById('formats'); 
            infoDiv.innerHTML = `
              <p><strong>Titre :</strong> ${data.title}</p>
              <p><strong>Durée :</strong> ${data.duration}</p>
              <p><strong>Formats disponibles :</strong> ${data.formats.map(f => f.type + ' (' + f.size + ')').join(', ')}</p>
            `;

            // Construire le lien de téléchargement
            const downloadUrl = `https://ytb-dw.social-networking.me/download.php?api_key=${apiKey}&url=${encodeURIComponent(youtubeUrl)}&format=${format}`;
            window.open(downloadUrl, '_blank');
          } else {
            alert("Erreur : " + data.error);
          }
        })
        .catch(error => {
          console.error('Erreur:', error);
          alert("Impossible de récupérer les informations de la vidéo.");
        });
    });
  </script>

</body>
</html>
