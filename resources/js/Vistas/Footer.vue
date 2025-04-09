<template>
    <footer class="footer">
        <div class="footer-header">
            <div class="logo">
                <img src="/img/wecollab.png" alt="Logo" class="logo-img" />
                <p class="logotipo">We collab</p>
            </div>

            <div class="social-media">
                <span class="fb"><i class="fa-brands fa-facebook"></i></span>
                <span class="tw"><i class="fa-brands fa-twitter"></i></span>
                <span class="in"><i class="fa-brands fa-instagram"></i></span>
                <span class="li"><i class="fa-brands fa-linkedin"></i></span>
                <span class="yt"><i class="fa-brands fa-youtube"></i></span>
            </div>
        </div>
        <hr />
        <div class="footer-section">
            <div>
                <h2>Company</h2>
                <a href="#">Home</a>
                <a href="#">Contact Us</a>
                <a href="#">About Us</a>
                <a href="#">Get Started</a>
            </div>
            <div>
                <h2>Services</h2>
                <a href="#">App Design</a>
                <a href="#">Web Design</a>
                <a href="#">Logo Design</a>
                <a href="#">Banner Design</a>
            </div>
            <div>
                <h2>Account</h2>
                <a href="#">Profile</a>
                <a href="#">My Account</a>
                <a href="#">Preferences</a>
                <a href="#">Purchase</a>
            </div>
            <div>
                <h2>Courses</h2>
                <a href="#">HTML y CSS</a>
                <a href="#">JavaScript</a>
                <a href="#">Photography</a>
                <a href="#">Photoshop</a>
            </div>
            <div>
                <h2>Suscribete</h2>
                <form action="">
                    <input type="email" placeholder="Enter your Email" />
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-rights">
            <p>
                &copy; {{ new Date().getFullYear() }}. Todos los derechos
                reservados
            </p>

            <p>Política de privacidad | Términos y Condiciones</p>
        </div>
    </footer>
</template>

<style scoped>
/* Reset de márgenes y padding */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Exo 2", sans-serif;
}

/* Estilos del footer */
.footer {
    background: #1e1e50; /* Azul oscuro de We Collab */
    width: 100%;
}

/* Logo */
.logo-img {
    width: 100px; /* Ajusta el tamaño del logo según necesidad */
    height: auto;
    margin-bottom: 10px; /* Agrega espacio entre el logo y el texto */
}

.logo {
    display: flex;
    flex-direction: column; /* Apila los elementos verticalmente */
    align-items: center; /* Centra los elementos horizontalmente */
    justify-content: center; /* Centra los elementos verticalmente */
    text-align: center; /* Asegura que el texto esté centrado */
}

.logotipo {
    font-size: 18px;
    font-weight: bold;
    color: white;
}

/* Redes sociales */
.social-media {
    display: flex;
    gap: 15px;
}

.social-media span {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: white;
    font-size: 20px;
}

/* Colores de cada ícono */
.fb {
    background: rgb(0, 81, 255);
}
.tw {
    background: rgb(0, 140, 255);
}
.in {
    background: rgb(255, 0, 179);
}
.li {
    background: rgb(0, 102, 255);
}
.yt {
    background: rgb(255, 0, 0);
}

/* Header */
.footer-header {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    align-items: center;
}

/* Secciones del footer */
.footer-section {
    display: flex;
    justify-content: space-evenly;
    gap: 40px;
    flex-wrap: wrap;
    padding: 20px;
}

.footer-section div {
    display: flex;
    flex-direction: column;
}

.footer-section div h2 {
    color: white;
    padding: 10px 0;
}

a {
    color: #c5c5f1;
    text-decoration: none;
    padding: 5px;
}

a:hover {
    color: white;
}

/* Formulario de suscripción */
form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

input {
    background: none;
    border: solid 2px white;
    padding: 10px 20px;
    color: white;
}

button {
    padding: 10px 20px;
    background: #ff4081;
    color: #ffffff;
    font-size: 18px;
    border: none;
    cursor: pointer;
}

button:hover {
    background: #d81b60;
    color: #fff;
}

/* Derechos de autor - Pegado abajo sin margen lateral */
.footer-rights {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    background: #0a0433;
    color: white;
    width: 100%;
}

/* Responsividad */
@media screen and (max-width: 768px) {
    .footer-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .social-media {
        margin-top: 10px;
    }

    .footer-section {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }

    .footer-rights {
        background: #0e0e3a; /* Azul oscuro más profundo */
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
}
</style>
