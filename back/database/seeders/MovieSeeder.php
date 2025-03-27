<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run()
    {
        Movie::create([
            'title' => 'The Monkey',
            'sinopsis' => 'Un mono de cuerda poseído aterroriza un parque temático, desatando muertes inexplicables. Los gemelos dueños del juguete deben enfrentar su pasado mientras luchan por sobrevivir a esta criatura sobrenatural que convierte su atracción familiar en una pesadilla sangrienta.',
            'duracion' => '98 minuts',
            'director' => 'Osgood Perkins',
            'actores' => 'Theo James, Tatiana Maslany, Christian Convery, Colin Brien, Adam Scott, Elijah Wood, Rohan Campbell, Sarah Levy, Osgood Perkins',
            'año' => 2025,
            'genero' => 'Terror, Comèdia',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1743029416/pyaru3dnh4azxn3ie51i.jpg',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743026931/bdinihzjxhoknlxhjwie.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'IMAX',
            'disponible_en_streaming' => true,
        ]);

        Movie::create([
            'title' => 'Captain America: Brave New World',
            'sinopsis' => 'El nuevo Capitán América descubre un experimento genético que crea soldados mutantes incontrolables. Con aliados inesperados, debe detener esta amenaza antes que estos supersoldados destruyan ciudades enteras en un mundo donde los héroes se han vuelto parte del sistema político.',
            'duracion' => '119 minuts',
            'director' => 'Julius Onah',
            'actores' => 'Anthony Mackie, Harrison Ford, Danny Ramirez, Shira Haas, Tim Blake Nelson, Carl Lumbly, Giancarlo Esposito, Liv Tyler, Xosha Roquemore',
            'año' => 2025,
            'genero' => 'Acció, Thriller, Ciència-ficció',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892790/wyukbwqnzbd75bnkxxoy.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743026592/ub8k3a51a55mscugk4aw.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'IMAX',
            'disponible_en_streaming' => true,
        ]);

        Movie::create([
            'title' => 'Cleaner',
            'sinopsis' => 'Un gas experimental transforma a la gente en seres violentos dentro de un rascacielos futurista. Un limpiador con pasado militar debe rescatar a su hermano atrapado, usando su ingenio para sobrevivir a esta amenaza biológica que convierte el edificio en una trampa mortal.',
            'duracion' => '96 minuts',
            'director' => 'Martin Campbell',
            'actores' => 'Daisy Ridley, Clive Owen, Taz Skylar, Flavia Watson, Ray Fearon, Rufus Jones, Richard Hope, Lee Boardman, Stella Stocker',
            'año' => 2025,
            'genero' => 'Acció, Thriller',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892751/aa2dz1oi3qkpwema5okv.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743026810/evktuzwpcb7nzdemzrda.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'IMAX',
            'disponible_en_streaming' => true,
        ]);

        Movie::create([
            'title' => 'Z Zone',
            'sinopsis' => 'Jóvenes rebeldes en una base militar secreta descubren experimentos con soldados genéticamente alterados. Cuando las criaturas escapan, deben huir de estas instalaciones mientras evitan ser capturados por los monstruosos resultados de pruebas que ahora deambulan libremente por los pasillos.',
            'duracion' => '83 minuts',
            'director' => 'Bilguun Chuluundorj',
            'actores' => 'Bilguun Chuluundorj, Purevjargal Erdenebileg, Bazarragchaa Byambajav',
            'año' => 2025,
            'genero' => 'Terror',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892659/oxsdeg5dcfnglcvb3i31.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027639/cdseeipvriqx0af3drl2.mp4',
            'idioma' => 'Mongol',
            'subtitulos' => false,
            'formato' => 'Digital',
            'disponible_en_streaming' => false,
        ]);

        Movie::create([
            'title' => 'Batman Ninja vs. Yakuza League',
            'sinopsis' => 'Batman es transportado a un Japón alternativo donde una Liga de la Justicia corrupta domina con poderes genéticos. Debe detener sus experimentos biológicos que amenazan múltiples dimensiones, enfrentándose a superhumanos creados por tecnología prohibida en este mundo de fusión entre lo orgánico y lo artificial.',
            'duracion' => '90 minuts',
            'director' => 'Jumpei Mizusaki, Shinji Takagi',
            'actores' => 'Koichi Yamadera, Yuki Kaji, Kengo Kawanishi, Daisuke Ono, Akira Ishida, Ayane Sakura, Akio Otsuka, Nobuyuki Hiyama, Romi Park',
            'año' => 2025,
            'genero' => 'Animació, Acció',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892627/qhwylvro42ztyhezgwzd.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027585/csd6ngdaepzrdvbpn3jb.mp4',
            'idioma' => 'Japonès',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => false,
        ]);

        Movie::create([
            'title' => 'FOG OF WAR',
            'sinopsis' => 'Marines derribados tras líneas enemigas descubren supersoldados genéticamente mejorados que superan todo lo conocido. En este futuro cercano, su entrenamiento y humanidad son puestos a prueba contra enemigos cuyas habilidades modificadas desafían las leyes de la naturaleza en una batalla por la supervivencia.',
            'duracion' => 'No especificat',
            'director' => 'Joshua DeFour, Marielle Woods, Paul Anderson',
            'actores' => 'Noah Gray-Cabey, Michael Grant',
            'año' => 2025,
            'genero' => 'Acció, Guerra',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892569/seurhvilvpkja0ideglv.jpg',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027504/qxhk9ohzz2ciskszsamr.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => false,
        ]);

        Movie::create([
            'title' => 'Vaiana 2',
            'sinopsis' => 'Vaiana descubre secretos ancestrales cuando criaturas mitológicas modificadas por magia antigua amenazan su isla. Esta aventura épica la lleva a dominar fuerzas desconocidas para restaurar el equilibrio natural, enfrentando desafíos que prueban su conexión con sus raíces y el océano.',
            'duracion' => '99 minuts',
            'director' => 'Dana Ledoux Miller,Jason Hand, David G. Derrick Jr.',
            'actores' => 'Auliʻi Cravalho, Dwayne Johnson, Hualālai Chung, Rose Matafeo, David Fane, Awhimai Fraser, Khaleesi Lambert-Tsuda, Temuera Morrison, Nicole Scherzinger',
            'año' => 2024,
            'genero' => 'Animació, Aventura',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892294/jh5gjgr1lscmz6vcsyrp.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027160/ahpgu57qxhnnhiipzhs0.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => true,
        ]);

        Movie::create([
            'title' => 'Counterattack',
            'sinopsis' => 'Operativos especiales rescatan rehenes en un laboratorio genético, sin saber que enfrentarán humanos mejorados con habilidades sobrehumanas. La misión se convierte en caos cuando estos sujetos de prueba se vuelven violentos, forzando a los soldados a luchar por su vida contra enemigos imposibles.',
            'duracion' => '85 minuts',
            'director' => 'Chava Cartas',
            'actores' => 'Luis Alberti, Noé Hernández, Leonardo Alonso, Luis Curiel, Guillermo Nava, David Calderón León, Mayra Batalla, Israel Islas, Ishbel Bautista',
            'año' => 2025,
            'genero' => 'Acció, Aventura, Thriller',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892257/uhkqms8wsre4rj2gvhp7.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027392/fc7a7lbakwqjqnxfevxp.mp4',
            'idioma' => 'Espanyol',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => true,
        ]);

        Movie::create([
            'title' => 'Jurassic World',
            'sinopsis' => 'Científicos crean un dinosaurio genéticamente modificado para revitalizar el parque, pero la criatura superinteligente escapa. Lo que debía ser la mayor atracción se convierte en su mayor amenaza, poniendo en peligro a miles de visitantes en Isla Nublar.',
            'duracion' => '124 minuts',
            'director' => 'Colin Trevorrow',
            'actores' => 'Chris Pratt, Bryce Dallas Howard, Irrfan Khan, Vincent D\'Onofrio, Ty Simpkins, Nick Robinson, Jake Johnson, Omar Sy, BD Wong',
            'año' => 2015,
            'genero' => 'Acció, Aventura, Ciència-ficció, Thriller',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742891453/rx7kqk38d4bam49rp6yv.jpg',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1742891476/fdlvbb7bpbe5n6wact8s.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => true,
        ]);

        Movie::create([
            'title' => 'Old Guy',
            'sinopsis' => 'Un sicario veterano entrena jóvenes asesinos genéticamente mejorados, pero su mejor aprendiz desarrolla efectos secundarios violentos. Debe detener a su protegido descontrolado antes que este desate el caos en el submundo criminal donde la modificación genética es común.',
            'duracion' => '93 minuts',
            'director' => 'Simon West',
            'actores' => 'Christoph Waltz, Lucy Liu, Cooper Hoffman, Karishma Navekar, Desmond Edwards, Ryan McParland, Ann Akinjirin, Charlie Hamblett, Tamara Chanel White',
            'año' => 2024,
            'genero' => 'Acció, Comèdia',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742891534/bkip27vaahbwejo6yanw.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027325/anahn6q1xyxrarxic4zw.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => false,
        ]);

        Movie::create([
            'title' => 'Mufasa: The Lion King',
            'sinopsis' => 'El joven Mufasa descubre su destino cuando una sequía altera el comportamiento animal en África. Debe aprender a gobernar no solo su reino, sino también las fuerzas naturales descontroladas que amenazan el equilibrio ecológico en esta precuela del Rey León.',
            'duracion' => '118 minuts',
            'director' => 'Barry Jenkins',
            'actores' => 'Aaron Pierre, Kelvin Harrison Jr., Tiffany Boone, Kagiso Lediga, Preston Nyman, Blue Ivy Carter, John Kani, Mads Mikkelsen, Seth Rogen',
            'año' => 2024,
            'genero' => 'Aventura, Família, Animació',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892189/hd5nkzrx17shbktrpotp.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027281/mx3kdmjwo5rdhm14pwpz.mp4',
            'idioma' => 'Anglès',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => false,
        ]);

        Movie::create([
            'title' => 'Sky Force',
            'sinopsis' => 'Pilotos mejorados genéticamente se vuelven incontrolables tras un experimento militar. Un comandante debe decidir entre salvar a sus hombres convertidos en armas vivientes o proteger al mundo de sus habilidades desatadas en este thriller aéreo de alto octanaje.',
            'duracion' => '125 minuts',
            'director' => 'Sandeep Kewlani, Abhishek Kapur',
            'actores' => 'Akshay Kumar, Veer Pahariya, Nimrat Kaur, Sara Ali Khan, Sharad Kelkar, Mohit Chauhan, Manish Chaudhary, Naisha Khanna, Soham Majumdar',
            'año' => 2025,
            'genero' => 'Acció, Thriller',
            'poster_url' => 'https://res.cloudinary.com/dbculqlgg/image/upload/v1742892219/xlzegnegskigqtnkpxcb.webp',
            'trailer_url' => 'https://res.cloudinary.com/dbculqlgg/video/upload/v1743027218/mdu70x0kndo96zxsgdsa.mp4',
            'idioma' => 'Hindi',
            'subtitulos' => true,
            'formato' => 'Digital',
            'disponible_en_streaming' => false,
        ]);                           
    }
}