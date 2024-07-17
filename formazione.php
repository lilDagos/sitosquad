<?php
// Definisci le variabili e inizializzale a zero di default
$difesa = 0;
$centrocampo = 0;
$attacco = 0;

// Recupera i valori dal form solo se sono stati inviati
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $difesa = isset($_POST['difesa']) ? (int)$_POST['difesa'] : 0;
    $centrocampo = isset($_POST['centrocampo']) ? (int)$_POST['centrocampo'] : 0;
    $attacco = isset($_POST['attacco']) ? (int)$_POST['attacco'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formazione</title>
    <style>
        body {
            background-image: url("campodacalcio.jpg");
            margin: 0;
            width: 100%;
            height: 100vh;
            background-position: bottom;
            background-repeat: no-repeat;
            background-color: olivedrab;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;

        }

        .container {
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 20px;
        }

        .slot-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 10px;
        }

        .slot {
            width: 200px; /* Larghezza del quadrato */
            height: 150px; /* Altezza del quadrato */
            margin: 10px;
            position: relative;
            border: 2px solid black;
            border-radius: 5px;
            overflow: hidden;
        }

        .slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nome {
            background-color: white;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 5px;
            text-align: center;
            font-size: 14px;
        }

        input[type="text"], input[type="file"] {
            display: block;
            margin: 10px auto;
            padding: 10px;
            height: 5%;
            width: 80%;
            max-width: 200px;
        }

        .bottone {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            margin: 0 auto;
        }

        .bottone:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Inserisci i dati della formazione</h1>
        <form id="formazioneForm" action="salva_formazione.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="formazione_id" value="<?php echo $formazione_id; ?>">
            
            <div class="section">
                <h2>Attacco</h2>
                <div class="slot-container" id="attaccoContainer">
                    <?php for ($i = 0; $i < $attacco; $i++): ?>
                    <div class="slot">
                        <input type="file" name="immagini_attacco[]" class="input-file">
                        <input type="hidden" name="immagini_attacco_base64[]" class="input-file-base64">
                        <input type="text" name="nomi_attacco[]" placeholder="Nome" class="nome">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="section">
                <h2>Centrocampo</h2>
                <div class="slot-container" id="centrocampoContainer">
                    <?php for ($i = 0; $i < $centrocampo; $i++): ?>
                    <div class="slot">
                        <input type="file" name="immagini_centrocampo[]" class="input-file">
                        <input type="hidden" name="immagini_centrocampo_base64[]" class="input-file-base64">
                        <input type="text" name="nomi_centrocampo[]" placeholder="Nome" class="nome">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="section">
                <h2>Difesa</h2>
                <div class="slot-container" id="difesaContainer">
                    <?php for ($i = 0; $i < $difesa; $i++): ?>
                    <div class="slot">
                        <input type="file" name="immagini_difesa[]" class="input-file">
                        <input type="hidden" name="immagini_difesa_base64[]" class="input-file-base64">
                        <input type="text" name="nomi_difesa[]" placeholder="Nome" class="nome">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <input class="bottone" type="submit" value="Salva Formazione">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputFiles = document.querySelectorAll('.input-file');

            inputFiles.forEach(inputFile => {
                inputFile.addEventListener('change', function() {
                    const slot = this.parentElement;
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = function() {
                            const img = new Image();
                            img.src = reader.result;
                            img.onload = function() {
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');
                                const maxSize = 150; // Dimensione massima dell'immagine
                                let width = img.width;
                                let height = img.height;
                                if (width > height) {
                                    if (width > maxSize) {
                                        height *= maxSize / width;
                                        width = maxSize;
                                    }
                                } else {
                                    if (height > maxSize) {
                                        width *= maxSize / height;
                                        height = maxSize;
                                    }
                                }
                                canvas.width = width;
                                canvas.height = height;
                                ctx.drawImage(img, 0, 0, width, height);
                                const dataURL = canvas.toDataURL('image/jpeg');
                                const imgPreview = new Image();
                                imgPreview.src = dataURL;
                                imgPreview.className = 'preview-img';
                                slot.appendChild(imgPreview);
                                slot.removeChild(inputFile);
                                const inputBase64 = document.createElement('input');
                                inputBase64.type = 'hidden';
                                inputBase64.name = inputFile.name.replace('immagini_', 'immagini_base64_');
                                inputBase64.value = dataURL;
                                slot.appendChild(inputBase64);
                            };
                        };
                    }
                });
            });
        });
    </script>
</body>
</html>
