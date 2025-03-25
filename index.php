<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter du texte sur un PDF</title>
    <style>
        #overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 774px;
            height: 1098px;
            background: transparent;
            margin-top: 6%;
            margin-left: 3%;
        }

        .draggable {
            position: absolute;
            cursor: grab;
            background: yellow;
            padding: 5px;
            border: 1px solid black;
        }

        /* Styles pour l'iframe */
        #pdf-container {
            position: relative;
            width: 824px;
            height: 1190px;
        }
    </style>
</head>

<body>
    <h2>Ajouter du texte à un PDF</h2>
    <form action="add_text.php" method="post" enctype="multipart/form-data">
        <label for="texte">Texte :</label>
        <input type="text" id="texte" name="texte" required>
        <button type="button" id="addText">Ajouter</button>
        <input type="hidden" name="x" id="x">
        <input type="hidden" name="y" id="y">
        <button type="submit" id="submitButton" disabled>Ajouter au PDF</button>
    </form>
    <h3>Glissez le texte à l'endroit souhaité sur le PDF.</h3>
    <div id="pdf-container">
        <iframe id="iframe" src="/uploads/v.pdf" style="height: 100%; width: 100%; border: none;"></iframe>
        <div id="overlay"></div>
    </div>
    <script>
        document.getElementById("addText").addEventListener("click", function() {
            var textValue = document.getElementById("texte").value;
            if (!textValue) return;

            var textElement = document.createElement("div");
            textElement.classList.add("draggable");
            textElement.textContent = textValue;
            textElement.style.left = "50px";
            textElement.style.top = "50px";

            document.getElementById("overlay").appendChild(textElement);
            makeDraggable(textElement);
        });

        function makeDraggable(element) {
            let offsetX, offsetY, isDragging = false;

            // Récupérer la taille de l'iframe et de la zone de superposition
            const iframe = document.getElementById("iframe");
            const iframeRect = iframe.getBoundingClientRect(); // Position et dimensions de l'iframe
            const overlay = document.getElementById("overlay");
            const overlayRect = overlay.getBoundingClientRect(); // Position et dimensions de l'overlay

            element.addEventListener("mousedown", function(e) {
                isDragging = true;

                // Calculer les offsets en tenant compte de l'élément
                offsetX = e.clientX - element.getBoundingClientRect().left;
                offsetY = e.clientY - element.getBoundingClientRect().top;

                // Changer le curseur pour "grabbing"
                element.style.cursor = "grabbing";
            });

            document.addEventListener("mousemove", function(e) {
                if (!isDragging) return;

                // Calculer les nouvelles positions par rapport à l'overlay
                let x = e.clientX - offsetX - overlayRect.left;
                let y = e.clientY - offsetY - overlayRect.top;

                // Appliquer les bornes pour que l'élément ne dépasse pas les bords de l'overlay
                x = Math.max(0, Math.min(x, overlayRect.width - element.offsetWidth));
                y = Math.max(0, Math.min(y, overlayRect.height - element.offsetHeight));

                // Déplacer l'élément
                element.style.left = x + "px";
                element.style.top = y + "px";
            });

            document.addEventListener("mouseup", function() {
                isDragging = false;
                element.style.cursor = "grab";

                // Sauvegarder les coordonnées dans le formulaire (en pourcentage par rapport à l'overlay)
                const overlayRect = document.getElementById("overlay").getBoundingClientRect();
                document.getElementById("x").value = (parseInt(element.style.left) / overlayRect.width) * 100;

                // Inverser la position Y pour ajuster la coordonnée (inversion de l'axe Y)
                const adjustedY = overlayRect.height - parseInt(element.style.top);
                document.getElementById("y").value = (adjustedY / overlayRect.height) * 100;

                // Activer le bouton de soumission
                document.getElementById("submitButton").disabled = false;
            });
        }
    </script>
</body>

</html>