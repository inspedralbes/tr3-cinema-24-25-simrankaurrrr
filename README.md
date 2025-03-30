# 🎬 MDVD CINEMES |  📝 DEMO
👉 Clica a la imatge per veure la demostració 

[![Demo](image.png)](https://youtu.be/zmet-AdSK9w?si=33pcTUKXYA0iDL3c)


## 📝 DESCRIPCIÓ BREU
MDVD Cinemes és una plataforma web moderna que permet als usuaris buscar pel·lícules, comprar entrades i gestionar les seves reserves de forma senzilla. Per als administradors, ofereix eines completes per gestionar la programació, sales i vendes del cinema.

## 🔍 INFORMACIÓ DEL PROJECTE

| 📌 Detall          | ℹ️ Informació |
|--------------------|---------------|
| **Autora**         | Gursimranjit Kaur |
| **Estat**          | Completat ✅ |
| **URL Producció**  | [http://mdvdcine.daw.inspedralbes.cat/](http://mdvdcine.daw.inspedralbes.cat/) |
| **URL Backend**    | [http://mdvdback.daw.inspedralbes.cat/](http://mdvdback.daw.inspedralbes.cat/) |
| **Gestor Tasques** | [Taiga](https://tree.taiga.io/project/simrankaur-tr3-cinema-simran/timeline) |
| **Prototip UI**    | [Penpot](https://design.penpot.app/#/view?file-id=456eee66-5663-80cb-8005-d35604cdc330) |

## ✨ CARACTERÍSTIQUES PRINCIPALS

<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">

🔍 **Cerca avançada**  
Filtra pel·lícules per gènere, idioma o any amb el nostre sistema intel·ligent

🎟 **Compra integrada**  
Sistema complet de reserva i compra d'entrades amb confirmació immediata

📅 **Gestió de reserves**  
Consulta el teu historial i gestiona les teves properes visites al cinema

👨‍💼 **Panell d'administració**  
Eines completes per a la gestió del cinema i programació

✉️ **Notificacions**  
Rep correus de confirmació amb els detalls de les teves compres

</div>

## 🛠 TECNOLOGIES UTILITZADES

<div style="display: flex; justify-content: space-between; margin: 20px 0;">

<div style="width: 48%; background: #f0f8ff; padding: 15px; border-radius: 8px;">

### **Frontend**
- Nuxt.js (Vue 3)
- Axios (crides API)
- Tailwind CSS

</div>

<div style="width: 48%; background: #fff0f5; padding: 15px; border-radius: 8px;">

### **Backend**
- Laravel 10
- MySQL
- Laravel Mail

</div>

</div>

## 🚀 INSTAL·LACIÓ LOCAL

### 📋 REQUISITS PREVIS
- Node.js v18+
- PHP 8.1+
- Composer
- MySQL 5.7+

### 🔧 CONFIGURACIÓ BACKEND

```bash
# Clonar el repositori
git clone [URL_DEL_REPO]

# Accedir al directori backend
cd back

# Instal·lar dependències
composer install

# Configurar entorn
cp .env.example .env

# Generar clau de l'aplicació
php artisan key:generate

# Configurar base de dades (editar .env)
DB_DATABASE=mdvd_cines
DB_USERNAME=el_teu_usuari
DB_PASSWORD=la_teva_contrasenya

# Executar migracions
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

### 🔧 Configuració Frontend

```bash
# Accedir al directori frontend
cd ../front

# Instal·lar dependències
npm install
npm install normalize.css

# Configurar connexió API (editar nuxt.config.ts)
apiBase: "http://localhost:8000/api"

# Iniciar aplicació
npm run dev

```
## 📄 LICÈNCIA
Aquest projecte està sota la llicència MIT. Veure LICENSE per a més detalls.

<div align="center"> ✨ Desenvolupat per <b>Gursimranjit Kaur</b> ✨ </div> 

