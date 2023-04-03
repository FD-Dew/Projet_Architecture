<?php
// paramètres de connexion à la base de données
$host = '10.5.40.52:3306';
$dbname = 'projarcapp';
$username = 'app';
$password = 'App!2023';

// tentative de connexion à la base de données en utilisant PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $newsletters = $pdo->query("SELECT * FROM arcapp");
    $newsletter = $newsletters->fetchAll(PDO::FETCH_ASSOC);

    $email = [];
    foreach ($newsletter as $news) {
        $email[] = $news['email'];
    }

    if(empty($_POST['email']) || strpos($_POST['email'], '@') == false) {
        echo 'Veuillez entrer une adresse mail valide';
    }else {
    if(in_array($_POST['email'], $email)){
        echo 'Vous êtes déjà inscrit à notre newsletter';
    } else {
        $sql = "INSERT INTO arcapp (email) VALUES (:email)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->execute();        
        echo 'Vous êtes bien abonné à notre newsletter';
        
        envoyermail();
        
    }
}
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}



function envoyermail(){
    // Créer une variable pour stocker l'URL de l'API Sendinblue pour l'envoi d'e-mails
$url = 'https://api.sendinblue.com/v3/emailCampaigns';

// Créer un tableau de données pour inclure les détails de l'e-mail que vous souhaitez envoyer
$data = array(
    "name" => "Ma campagne",
    "subject" => "Objet de ma campagne",
    "sender" => array(
        "name" => "Nom de l'expéditeur",
        "email" => $_POST['email'],
    ),
    "type" => "classic",
    "htmlContent" => "<html><body><h1>Contenu HTML de la campagne</h1></body></html>",
    "listIds" => array(1,2,3)
);

// Convertir le tableau de données en JSON
$payload = json_encode($data);

// Créer un tableau d'en-têtes pour inclure votre clé d'API Sendinblue et spécifier le type de contenu de la requête
$headers = array(
    "Content-Type: application/json",
    "api-key: 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"
);

// Initialiser une session cURL pour effectuer la requête à l'API Sendinblue
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => $headers,
));

// Exécuter la requête cURL et stocker les données de réponse dans une variable
$response = curl_exec($curl);

// Fermer la session cURL
curl_close($curl);

// Traiter les données de réponse, par exemple en les convertissant en un tableau PHP
$result = json_decode($response, true);

// Afficher les données de réponse pour le débogage
echo ' Vous allez recevoir un mail';
}

?>

