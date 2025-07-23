<?php
    if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["phone"])) {
        $nome = trim(strip_tags($_POST["name"]));
        $email = trim(strip_tags($_POST["email"]));
        $whatsapp = trim(strip_tags($_POST["phone"]));

        try {
            // Conecta ao banco de dados SQLite
            $db = new PDO('sqlite:server/leads.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepara o comando de inserção
            $stmt = $db->prepare("INSERT INTO leads (nome, email, whatsapp) VALUES (:nome, :email, :whatsapp)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':whatsapp', $whatsapp);

            // Executa a inserção
            $stmt->execute();

            setcookie("session", base64_encode("true_access"), time() + 3600);

            header("Location: index.php");
        } catch (PDOException $e) {
            echo "Erro ao inserir no banco de dados: " . $e->getMessage();
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- SEO -->
        <meta name="description" content="O Desafio 21 Dias do Disciplina Diária promove uma grande mudança de rotina e produtividade através da modulação de hábitos simples e consistentes. Faça parte da comunidade e mude seu corpo, mentalidade e rotina em 21 dias!">
        <meta name="author" content="Disciplina Diária">
        <meta name="robots" content="index, follow">
        <!-- Social Media -->
        <meta property="og:title" content="Disciplina Diária - Desafio 21 Dias">
        <meta property="og:description" content="O Desafio 21 Dias do Disciplina Diária promove uma grande mudança de rotina e produtividade através da modulação de hábitos simples e consistentes. Faça parte da comunidade e mude seu corpo, mentalidade e rotina em 21 dias!">
        <meta property="og:image" content="https://randompg.discloud.app/img/dd.png">
        <meta property="og:url" content="https://projetodd.discloud.app/">
        <meta property="og:type" content="website">
        <title>Disciplina Diária - Desafio 21 Dias</title>
        <link rel="icon" type="image/x-icon" href="img/dd.png">
        <!-- CSS Personalizado -->
        <link rel="stylesheet" href="css/main.css">
        <!-- BOX Icons -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
        <div id="canvas-bg"></div>
        <div id="root">
            <div class="big-title">
                <h1><span class="main">Disciplina</span><span class="second">Diária</span></h1>
                <h3>Mude sua vida em 21 dias.</h3>
            </div>
            <div class="content">
                <p>
                    <?php
                        if (!isset($_COOKIE['session']) || base64_decode($_COOKIE['session']) !== "true_access") {
                            echo 'Corpo, mente e rotina. A mudança começa com o primeiro passo.<br><br>Preencha os campos abaixo e desbloqueie o desafio.';   
                        } else {
                            echo '<b>Desafio desbloqueado!</b><br><br>Baixe o planner e o guia para começar!';
                        }
                    ?>
                </p>
            </div>
            <div class="form">            
                <?php
                    if (!isset($_COOKIE['session']) || base64_decode($_COOKIE['session']) !== "true_access") {
                        echo '
                        <form action="" method="POST" class="lead-form">
                            <input type="text" name="name" placeholder="Seu nome" minlength="3" maxlength="64" required>
                            <input type="email" name="email" placeholder="Seu melhor e-mail" maxlength="64" required>
                            <input type="tel" name="phone" placeholder="WhatsApp" required>
                            <button type="submit">QUERO RECEBER O DESAFIO</button>
                        </form>';   
                    } else {
                        echo '
                        <div class="unlocked-items">
                            <a class="download-pdf" href="pdf/Desafio21Dias.pdf" download>Baixar Desafio 21 Dias</a>
                            <a class="download-pdf" href="pdf/Guia21Dias.pdf" download>Baixar Guia do Desafio</a>
                        </div>
                        ';
                    }
                ?>
            </div>
            <div class="footer">
                <span>Desenvolvido por <a href="https://br.pinterest.com/projetodd/" target="_blank" class="contact-me">Disciplina Diária</a></span>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
        <script>
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ alpha: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.getElementById('canvas-bg').appendChild(renderer.domElement);
    
            const particlesGeometry = new THREE.BufferGeometry();
            const particlesCount = 800;
            const posArray = new Float32Array(particlesCount * 3);
    
            for (let i = 0; i < particlesCount * 3; i++) {
                posArray[i] = (Math.random() - 0.5) * 150;
            }
    
            particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
            const particlesMaterial = new THREE.PointsMaterial({
                size: 0.2,
                color: 0xFF3030,
                transparent: true,
                opacity: 0.7,
                blending: THREE.AdditiveBlending
            });
    
            const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
            scene.add(particlesMesh);
    
            camera.position.z = 50;
    
            // Animação suave
            function animate() {
                requestAnimationFrame(animate);
                particlesMesh.rotation.x += 0.0005;
                particlesMesh.rotation.y += 0.001;
                const positions = particlesMesh.geometry.attributes.position.array;
                for (let i = 0; i < particlesCount * 3; i += 3) {
                    positions[i + 2] += Math.sin(Date.now() * 0.001 + positions[i]) * 0.01;
                    if (positions[i + 2] > 75) positions[i + 2] = -75;
                }
                particlesMesh.geometry.attributes.position.needsUpdate = true;
                renderer.render(scene, camera);
            }
            animate();

            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
        </script>
        <script src="https://unpkg.com/inputmask@5.0.8/dist/inputmask.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Inputmask({"mask": "(99) 99999-9999"}).mask(document.querySelector('input[name="phone"]'));
            });
        </script>
    </body>
</html>