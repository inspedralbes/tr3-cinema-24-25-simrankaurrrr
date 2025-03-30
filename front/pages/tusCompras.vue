<template>
    <Navbar />

    <div class="my-tickets-page">
        <button @click="goBack" class="back-button">
            ⬅ Tornar
        </button>
        <div class="tickets-container">
            <h1>Les Meves Entrades</h1>

            <div class="tabs">
                <button @click="activeTab = 'future'" :class="{ active: activeTab === 'future' }">
                    🎟️ Pròximes
                </button>
                <button @click="activeTab = 'pending'" :class="{ active: activeTab === 'pending' }">
                    ⏳ En Procés
                </button>
                <button @click="activeTab = 'past'" :class="{ active: activeTab === 'past' }">
                    📜 Històric
                </button>
            </div>

            <div v-if="loading" class="loading">
                <div class="spinner"></div>
                <p>Carregant les teves entrades...</p>
            </div>

            <div v-else-if="error" class="error-message">
                <p>⚠️ Error en carregar les teves entrades: {{ error }}</p>
                <button @click="fetchTickets" class="retry-btn">Reintentar</button>
            </div>

            <div v-else>
                <!-- Entrades Futures -->
                <div v-if="activeTab === 'future'">
                    <div class="section-header">
                        <h2>🎬 Les Teves Pròximes Entrades</h2>
                        <p>Aquestes són les entrades per a les teves pròximes sessions de cinema</p>
                    </div>

                    <div v-if="futureTickets.length > 0" class="tickets-grid">
                        <div v-for="ticket in futureTickets" :key="ticket.id" class="ticket-card future">
                            <div class="ticket-poster">
                                <img :src="ticket.movie_poster || 'https://via.placeholder.com/300x450'"
                                    :alt="ticket.movie_title">
                            </div>
                            <div class="ticket-info">
                                <div class="ticket-header">
                                    <h3>{{ ticket.movie_title }}</h3>
                                    <span class="ticket-status confirmed">Confirmada</span>
                                </div>

                                <div class="ticket-details">
                                    <p><strong>📅 Data:</strong> {{ formatDate(ticket.session_date) }}</p>
                                    <p><strong>🕒 Hora:</strong> {{ ticket.session_time }}</p>
                                    <p><strong>💺 Seients:</strong> {{ formatSeats(ticket.butaca_ids) }}</p>
                                    <p><strong>💰 Total:</strong> {{ formatPrice(ticket.total_amount) }}</p>
                                </div>

                                <div class="ticket-actions">
                                    <button class="action-btn view-btn" @click="viewMovie(ticket.movie_id)">
                                        Veure Pel·lícula
                                    </button>
                                    <button class="action-btn download-btn" @click="downloadTicket(ticket.id)">
                                        Descarregar Entrada
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="empty-section">
                        <p>No tens entrades per a pròximes sessions</p>
                        <router-link to="/" class="browse-btn">Explorar Pel·lícules</router-link>
                    </div>
                </div>

                <!-- Reserves en Procés -->
                <div v-if="activeTab === 'pending'">
                    <div class="section-header">
                        <h2>⏳ Reserves en Procés</h2>
                        <p>Aquestes entrades estan al teu carret pendents de confirmació</p>
                    </div>

                    <div v-if="pendingTickets.length > 0" class="tickets-grid">
                        <div v-for="reserva in pendingTickets" :key="reserva.reserva_id" class="ticket-card pending">
                            <div class="ticket-poster">
                                <img :src="reserva.movie_poster || 'https://via.placeholder.com/300x450'"
                                    :alt="reserva.movie_title">
                                <div class="pending-overlay">
                                    <span>Al Carret</span>
                                </div>
                            </div>
                            <div class="ticket-info">
                                <div class="ticket-header">
                                    <h3>{{ reserva.movie_title }}</h3>
                                    <span class="ticket-status pending">Reservada</span>
                                </div>

                                <div class="ticket-details">
                                    <p><strong>📅 Data:</strong> {{ formatDate(reserva.session_date) }}</p>
                                    <p><strong>🕒 Hora:</strong> {{ reserva.session_time }}</p>
                                    <p><strong>💺 Seient:</strong> {{ formatSeat(reserva.butaca) }}</p>
                                    <p><strong>💰 Preu:</strong> {{ formatPrice(reserva.total_amount) }}</p>
                                </div>

                                <div class="ticket-actions">
                                    <button class="action-btn pay-btn" @click="completePayment()">
                                        Completar Pagament
                                    </button>
                                    <button class="action-btn cancel-btn" @click="eliminarReserva(reserva)">
                                        ❌ Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="empty-section">
                        <p>No tens reserves al teu carret</p>
                        <router-link to="/" class="browse-btn">Explorar Pel·lícules</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import communicationManager from '@/services/communicationManager';
import Navbar from '@/components/Navbar.vue';
import { jsPDF } from "jspdf";

export default {
    components: {
        Navbar
    },
    setup() {
        const router = useRouter();
        const tickets = ref([]);
        const loading = ref(true);
        const error = ref(null);
        const activeTab = ref('future');
        const carritoReservas = ref([]);
        const showModal = ref(false);
        const currentRating = ref(0);
        const currentTicket = ref(null);
        const currentMovieTitle = ref('');

        const eliminarReserva = async (reserva) => {
            try {
                await communicationManager.eliminarReserva(reserva.reserva_id);
                carritoReservas.value = carritoReservas.value.filter(
                    r => r.reserva_id !== reserva.reserva_id
                );
                console.log('Reserva eliminada correctamente');
            } catch (err) {
                console.error('Error eliminando reserva:', err);
                error.value = 'No se pudo eliminar la reserva';
            }
        };

        const fetchTickets = async () => {
            try {
                loading.value = true;
                error.value = null;

                const currentUser = await communicationManager.getCurrentUser();
                const userTickets = await communicationManager.getComprasPorUsuario(currentUser.id);

                const ticketsWithDetails = await Promise.all(
                    userTickets.map(async ticket => {
                        try {
                            const sessionDetails = await communicationManager.getSessionById(ticket.movie_session_id);
                            const movieDetails = await communicationManager.getMovieById(sessionDetails.movie_id);

                            return {
                                ...ticket,
                                butaca_ids: JSON.parse(ticket.butaca_ids),
                                movie_title: movieDetails.title,
                                movie_poster: movieDetails.poster_url || 'https://via.placeholder.com/300x450',
                                session_date: sessionDetails.session_date,
                                session_time: sessionDetails.session_time,
                                room_name: sessionDetails.room_name,
                                movie_id: sessionDetails.movie_id,
                                estado: ticket.estado
                            };
                        } catch (err) {
                            console.error('Error obteniendo detalles para ticket', ticket.id, err);
                            return {
                                ...ticket,
                                butaca_ids: JSON.parse(ticket.butaca_ids),
                                movie_title: `Sesión ${ticket.movie_session_id}`,
                                movie_poster: 'https://via.placeholder.com/300x450',
                                session_date: 'Fecha no disponible',
                                session_time: 'Hora no disponible',
                                movie_id: ticket.movie_session_id,
                                estado: ticket.estado
                            };
                        }
                    })
                );

                tickets.value = ticketsWithDetails;
                await fetchCarritoReservas();
            } catch (err) {
                console.error('Error al obtener entradas:', err);
                error.value = err.message || 'No se pudieron cargar tus entradas';
            } finally {
                loading.value = false;
            }
        };

        const fetchCarritoReservas = async () => {
            try {
                const carritoData = await communicationManager.getCarrito();
                console.log("Datos CRUDOS del carrito:", carritoData);
                
                const reservasConDetalles = await Promise.all(
                    carritoData.map(async reserva => {
                        try {
                            const sessionDetails = await communicationManager.getSessionById(reserva.session_id);
                            const movieDetails = await communicationManager.getMovieById(sessionDetails.movie_id);

                            return {
                                id: reserva.reserva_id,
                                movie_title: movieDetails.title,
                                movie_poster: movieDetails.poster_url || 'https://via.placeholder.com/300x450',
                                session_date: sessionDetails.session_date,
                                session_time: sessionDetails.session_time,
                                room_name: sessionDetails.room_name,
                                movie_id: sessionDetails.movie_id,
                                butaca: {
                                    fila: reserva.fila,
                                    columna: reserva.columna
                                },
                                total_amount: Number(reserva.precio) || 0,
                                estado: 'en_proceso',
                                reserva_id: reserva.reserva_id,
                                fila: reserva.fila,
                                columna: reserva.columna,
                                nombre_pelicula: reserva.nombre_pelicula
                            };
                        } catch (err) {
                            console.error('Error obteniendo detalles para reserva', reserva.reserva_id, err);
                            return {
                                id: reserva.reserva_id,
                                movie_title: reserva.nombre_pelicula || `Sesión ${reserva.session_id}`,
                                movie_poster: 'https://via.placeholder.com/300x450',
                                session_date: 'Fecha no disponible',
                                session_time: 'Hora no disponible',
                                butaca: {
                                    fila: reserva.fila || 0,
                                    columna: reserva.columna || 0
                                },
                                total_amount: Number(reserva.precio) || 0,
                                estado: 'en_proceso',
                                reserva_id: reserva.reserva_id,
                                fila: reserva.fila || 0,
                                columna: reserva.columna || 0,
                                nombre_pelicula: reserva.nombre_pelicula
                            };
                        }
                    })
                );

                carritoReservas.value = reservasConDetalles;
            } catch (err) {
                console.error('Error al obtener el carrito:', err);
                carritoReservas.value = [];
            }
        };

        const formatSeat = (seat) => {
    if (!seat) return 'No disponible';
    
    // Si es un objeto con fila/columna
    if (seat.fila !== undefined && seat.columna !== undefined) {
        const filaChar = String.fromCharCode(65 + (Number(seat.fila) || 0));
        const columnaNum = (Number(seat.columna) || 0); // <- Quitamos el +1 aquí
        return `${filaChar}${columnaNum}`;
    }
    
    // Si es un ID numérico
    if (typeof seat === 'number') {
        const fila = String.fromCharCode(65 + Math.floor((seat - 1) / 10));
        const numero = ((seat - 1) % 10); // <- Quitamos el +1 aquí
        return `${fila}${numero}`;
    }
    
    return 'No disponible';
};

        const formatSeats = (seats) => {
            if (!seats || !Array.isArray(seats)) return 'No disponible';
            return seats.map(seatId => formatSeat(seatId)).join(', ');
        };

        const formatPrice = (amount) => {
            const num = Number(amount);
            return isNaN(num) ? '0.00€' : num.toFixed(2) + '€';
        };

        const formatDate = (dateString) => {
            if (!dateString) return 'Fecha no disponible';
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('es-ES', options);
        };

        const futureTickets = computed(() => {
            return tickets.value.filter(ticket => {
                const isPaid = ticket.estado === 'pagado';
                const isFuture = new Date(ticket.session_date) > new Date();
                return isPaid && isFuture;
            });
        });

        const pendingTickets = computed(() => {
            return carritoReservas.value;
        });

        const pastTickets = computed(() => {
            return tickets.value.filter(ticket => {
                const isPaid = ticket.estado === 'pagado';
                const isPast = new Date(ticket.session_date) < new Date();
                return isPaid && isPast;
            });
        });

        const viewMovie = (movieId) => {
            router.push(`/movies/${movieId}`);
        };

        const completePayment = async () => {
            router.push(`/compra`);
        };

        const downloadTicket = (ticketId) => {
            const ticket = tickets.value.find(t => t.id === ticketId);
            if (!ticket) return;

            // Configuración del documento (85x55mm - tamaño estándar de entrada)
            const doc = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: [85, 55]
            });

            // Dimensiones del documento
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();

            // Margen lateral
            const margin = 10;

            // Función para texto centrado
            const centeredText = (text, y, fontSize = 10, isBold = false) => {
                doc.setFontSize(fontSize);
                doc.setFont("helvetica", isBold ? "bold" : "normal");
                const textWidth = doc.getStringUnitWidth(text) * fontSize / doc.internal.scaleFactor;
                const x = (pageWidth - textWidth) / 2;
                doc.text(text, x, y);
            };

            // 1. Fondo
            doc.setFillColor(43, 45, 66); // Azul oscuro
            doc.rect(0, 0, pageWidth, pageHeight, 'F');

            // 2. Encabezado con color
            doc.setFillColor(239, 35, 60); // Rojo
            doc.rect(0, 0, pageWidth, 12, 'F');

            // Texto del encabezado
            doc.setTextColor(237, 242, 244); // Blanco
            centeredText("ENTRADA DE CINE", 8, 10, true);

            // 3. Línea divisoria
            doc.setDrawColor(237, 242, 244); // Blanco
            doc.setLineWidth(0.3);
            doc.line(margin, 14, pageWidth - margin, 14);

            // 4. Título de la película (con manejo de caracteres especiales)
            const movieTitle = ticket.movie_title
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .substring(0, 25)
                .toUpperCase();

            doc.setTextColor(237, 242, 244); // Blanco
            centeredText(movieTitle, 22, 9, true);

            // 5. Detalles de la entrada
            doc.setFontSize(7);
            doc.setTextColor(237, 242, 244); // Blanco

            // Formatear fecha
            const options = { weekday: 'short', day: '2-digit', month: 'short' };
            const formattedDate = new Date(ticket.session_date)
                .toLocaleDateString('es-ES', options);

            centeredText(`Fecha: ${formattedDate}`, 28);
            centeredText(`Hora: ${ticket.session_time.substring(0, 5)}`, 32);
            centeredText(`Butaca: ${formatSeat(ticket.butaca_ids[0])}`, 40);
            centeredText(`Precio: ${ticket.total_amount} €`, 36);
            // 6. Código de ticket
            doc.setFontSize(5);
            doc.setTextColor(150, 150, 150); // Gris

            // Línea divisoria
            const lineLength = 40;
            const lineX = (pageWidth - lineLength) / 2;
            doc.line(lineX, 48, lineX + lineLength, 48);

            centeredText(`Emisión: ${new Date().toLocaleDateString('es-ES')}`, 54);

            // 7. Guardar el PDF
            const safeFilename = `Entrada_${movieTitle.replace(/\s+/g, '_')}_${ticket.id}.pdf`;
            doc.save(safeFilename);
        };



        const downloadReceipt = (ticketId) => {
            const ticket = tickets.value.find(t => t.id === ticketId);
            if (!ticket) {
                console.error("Entrada no encontrada");
                return;
            }

            try {
                const doc = new jsPDF();

                // Colores
                const primaryColor = [239, 35, 60]; // Rojo
                const secondaryColor = [43, 45, 66]; // Azul oscuro

                // Encabezado
                doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
                doc.rect(0, 0, 210, 30, 'F');
                doc.setFontSize(20);
                doc.setTextColor(255, 255, 255);
                doc.text('FACTURA DE COMPRA', 105, 20, { align: 'center' });

                // Información de la factura
                doc.setFontSize(12);
                doc.setTextColor(0, 0, 0);
                doc.text(`Factura #${ticket.id}`, 20, 45);
                doc.text(`Fecha: ${new Date().toLocaleDateString()}`, 20, 50);

                // Detalles de la película
                doc.setFontSize(14);
                doc.text(ticket.movie_title, 20, 65);

                doc.setFontSize(12);
                doc.text(`Fecha de la sesión: ${ticket.session_date}`, 20, 75);
                doc.text(`Hora: ${ticket.session_time}`, 20, 80);
                doc.text(`Sala: ${ticket.room_name || 'N/A'}`, 20, 85);
                doc.text(`Butacas: ${ticket.butaca_ids.join(', ')}`, 20, 90);

                // Tabla de precios
                doc.setFontSize(12);
                doc.setTextColor(secondaryColor[0], secondaryColor[1], secondaryColor[2]);
                doc.text('Concepto', 20, 110);
                doc.text('Cantidad', 120, 110);
                doc.text('Precio', 160, 110);
                doc.text('Total', 190, 110);

                doc.setDrawColor(200, 200, 200);
                doc.line(20, 112, 190, 112);

                doc.setFontSize(10);
                doc.setTextColor(0, 0, 0);
                doc.text('Entrada general', 20, 120);
                doc.text(ticket.butaca_ids.length.toString(), 120, 120, { align: 'right' });
                doc.text('10.00 €', 160, 120, { align: 'right' });
                doc.text(`${(ticket.butaca_ids.length * 10).toFixed(2)} €`, 190, 120, { align: 'right' });

                // Total
                doc.setFontSize(12);
                doc.setFont("helvetica", "bold");
                doc.text('TOTAL:', 160, 140);
                doc.text(`${ticket.total_amount} €`, 190, 140, { align: 'right' });

                // Pie de página
                doc.setFontSize(10);
                doc.setTextColor(150, 150, 150);
                doc.text('Gracias por su compra', 105, 280, { align: 'center' });
                doc.text('CineApp - Todos los derechos reservados', 105, 285, { align: 'center' });

                // Guardar el PDF
                const filename = `Factura_${ticket.movie_title.replace(/[^\w]/g, '_')}_${ticket.id}.pdf`;
                doc.save(filename);

            } catch (error) {
                console.error("Error al generar la factura:", error);
                alert("Error al generar la factura. Por favor inténtalo de nuevo.");
            }
        };

        const getStars = (rating) => {
            return '★'.repeat(rating) + '☆'.repeat(5 - rating);
        };

        const showRatingModal = (ticket) => {
            currentTicket.value = ticket;
            currentMovieTitle.value = ticket.movie_title;
            currentRating.value = ticket.rating || 0;
            showModal.value = true;
        };

        const closeModal = () => {
            showModal.value = false;
            currentRating.value = 0;
            currentTicket.value = null;
        };

        const setRating = (rating) => {
            currentRating.value = rating;
        };

        const submitRating = async () => {
            try {
                // Aquí iría la lógica para guardar el rating en el backend
                // Por ahora simulamos que se guarda
                if (currentTicket.value) {
                    currentTicket.value.rating = currentRating.value;
                    alert(`¡Gracias por valorar "${currentMovieTitle.value}" con ${currentRating.value} estrellas!`);
                }
                closeModal();
            } catch (err) {
                console.error('Error al guardar la valoración:', err);
                alert('No se pudo guardar la valoración');
            }
        };

        onMounted(fetchTickets);

        return {
            tickets,
            loading,
            error,
            activeTab,
            eliminarReserva,
            futureTickets,
            pendingTickets,
            pastTickets,
            fetchTickets,
            formatDate,
            formatSeat,
            formatSeats,
            viewMovie,
            completePayment,
            downloadTicket,
            downloadReceipt,
            getStars,
            showRatingModal,
            closeModal,
            setRating,
            submitRating,
            carritoReservas, 
            showModal,
            currentRating,
            currentMovieTitle,
            formatPrice
        };
    }
};
</script>

<style scoped>
/* Estilos para el botón de eliminar - igual que en el carrito */
.action-btn.cancel-btn {
    background-color: #d80032;
    color: #edf2f4;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
    margin-top: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.action-btn.cancel-btn:hover {
    background-color: #691e06;
    transform: translateY(-2px);
}

/* Estilo para el overlay de reservado */
.pending-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 193, 7, 0.7);
    color: #2b2d42;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 1.2rem;
}

/* Ajustes para la tarjeta de reserva */
.ticket-card.pending {
    border-left: 4px solid #FFC107;
}

.ticket-status.pending {
    background-color: #FFC107;
    color: #2b2d42;
}

.back-button {
    background-color: #2b2d42;
    color: #8d99ae;
    border: 2px solid #8d99ae;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    margin: 20px;
    font-size: 1rem;
    position: relative;
    /* Añade esto */
    z-index: 10;
    /* Asegura que esté por encima de otros elementos */
}

.back-button:hover {
    color: #ef233c;
    border-color: #ef233c;
}

/* Unificar estilos de overlay */
.pending-overlay,
.past-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    text-transform: uppercase;
}

.pending-overlay {
    background-color: rgba(255, 193, 7, 0.7);
    color: #2b2d42;
}

.past-overlay {
    background-color: rgba(108, 117, 125, 0.7);
    color: white;
}

.my-tickets-page {
    background-color: #2b2d42;
    min-height: 100vh;
    color: #edf2f4;
    padding: 20px;
}

.tickets-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    color: #ef233c;
    margin-bottom: 30px;
    text-align: center;
    font-size: 2.5rem;
}

.tabs {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
    gap: 15px;
}

.tabs button {
    padding: 12px 25px;
    background-color: #8d99ae;
    color: #2b2d42;
    border: none;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tabs button.active {
    background-color: #ef233c;
    color: #edf2f4;
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(239, 35, 60, 0.3);
}

.tabs button:hover:not(.active) {
    background-color: #d80032;
    color: #edf2f4;
}

.section-header {
    text-align: center;
    margin-bottom: 30px;
}

.section-header h2 {
    color: #edf2f4;
    font-size: 1.8rem;
    margin-bottom: 10px;
}

.section-header p {
    color: #8d99ae;
    font-size: 1.1rem;
}

.tickets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.ticket-card {
    background-color: #edf2f4;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s, box-shadow 0.3s;
    color: #2b2d42;
    display: flex;
}

.ticket-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.ticket-poster {
    width: 40%;
    position: relative;
    overflow: hidden;
}

.ticket-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}



.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.ticket-header h3 {
    margin: 0;
    font-size: 1.3rem;
    color: #2b2d42;
}

.ticket-status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.ticket-status.confirmed {
    background-color: #4CAF50;
    color: white;
}

.ticket-status.pending {
    background-color: #FFC107;
    color: #2b2d42;
}

.ticket-status.used {
    background-color: #6c757d;
    color: white;
}

.ticket-details {
    flex-grow: 1;
}

.ticket-details p {
    margin: 8px 0;
    font-size: 0.95rem;
}

.ticket-details strong {
    color: #2b2d42;
}

.expiry-warning {
    color: #d80032;
    font-weight: bold;
}

.ticket-info {
    width: 60%;
    padding: 20px;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    /* Añadido para incluir padding en el ancho */
}

.ticket-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    padding: 0 5px;
    /* Añadido para espacio en los lados */
}

.action-btn {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s;
    flex-grow: 1;
}

.view-btn {
    background-color: #8d99ae;
    color: #2b2d42;
}

.view-btn:hover {
    background-color: #6c757d;
    color: white;
}

.download-btn,
.receipt-btn {
    background-color: #2b2d42;
    color: white;
}

.download-btn:hover,
.receipt-btn:hover {
    background-color: #1a1b2a;
}

.pay-btn {
    background-color: #4CAF50;
    color: white;
}

.pay-btn:hover {
    background-color: #3e8e41;
}

.cancel-btn {
    background-color: #f44336;
    color: white;
}

.cancel-btn:hover {
    background-color: #d32f2f;
}

.ticket-card.past {
    opacity: 0.9;
    background-color: #f8f9fa;
}

.ticket-card.past:hover {
    opacity: 1;
}

.rate-btn {
    background: none;
    border: none;
    color: #ef233c;
    text-decoration: underline;
    cursor: pointer;
    padding: 0;
    margin-top: 10px;
    font-size: 0.9rem;
}

.empty-section {
    text-align: center;
    padding: 40px;
    background-color: rgba(141, 153, 174, 0.1);
    border-radius: 10px;
    margin-top: 30px;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin-bottom: 20px;
    opacity: 0.7;
}

.empty-section p {
    font-size: 1.2rem;
    color: #8d99ae;
    margin-bottom: 20px;
}

.browse-btn {
    padding: 10px 20px;
    background-color: #ef233c;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.browse-btn:hover {
    background-color: #d80032;
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    color: #8d99ae;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #8d99ae;
    border-top-color: #ef233c;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.error-message {
    text-align: center;
    padding: 20px;
    background-color: #691e06;
    border-radius: 8px;
    margin: 20px 0;
}

.error-message p {
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.retry-btn {
    padding: 10px 20px;
    background-color: #ef233c;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s;
}

.retry-btn:hover {
    background-color: #d80032;
}

/* Estilos para el modal de rating */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #edf2f4;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    color: #2b2d42;
    text-align: center;
}

.modal-content h3 {
    margin-top: 0;
    color: #ef233c;
}

.rating-stars {
    font-size: 2.5rem;
    margin: 20px 0;
    cursor: pointer;
}

.rating-stars span {
    color: #8d99ae;
    transition: color 0.2s;
}

.rating-stars span.active {
    color: #FFD700;
}

.rating-stars span:hover {
    color: #FFD700;
}

.modal-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.modal-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.modal-btn.confirm {
    background-color: #4CAF50;
    color: white;
}

.modal-btn.confirm:hover {
    background-color: #3e8e41;
}

.modal-btn.cancel {
    background-color: #f44336;
    color: white;
}

.modal-btn.cancel:hover {
    background-color: #d32f2f;
}

.rating-display .stars {
    color: #FFD700;
}

@media (max-width: 768px) {
    .tickets-grid {
        grid-template-columns: 1fr;
    }

    .ticket-card {
        flex-direction: column;
    }

    .ticket-poster,
    .ticket-info {
        width: 100%;
    }

    .ticket-info {
        padding: 15px;
        /* Reducido para móviles */
    }

    .tabs {
        flex-direction: column;
        align-items: center;
    }

    .tabs button {
        width: 100%;
        justify-content: center;
    }

    /* Botones en móviles */
    .ticket-actions {
        flex-direction: column;
        gap: 8px;
        padding: 0 10px;
        /* Más espacio en los lados */
    }

    .action-btn {
        width: 100%;
        padding: 10px;
        font-size: 0.9rem;
        margin: 0;
        /* Asegura que no haya márgenes adicionales */
    }

    .ticket-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .ticket-status {
        align-self: flex-start;
    }
}

@media (max-width: 576px) {
    .ticket-card {
        min-width: 280px;
    }

    .ticket-details p {
        font-size: 0.85rem;
        margin: 6px 0;
    }

    .ticket-header h3 {
        font-size: 1.1rem;
    }

    /* Más espacio para botones en pantallas muy pequeñas */
    .ticket-actions {
        padding: 0 15px;
    }
}

@media (max-width: 400px) {
    .ticket-poster {
        height: 150px;
    }

    .ticket-info {
        padding: 12px;
    }
}

@media (max-width: 350px) {
    .action-btn span.text {
        display: none;
    }

    .action-btn::before {
        content: attr(data-icon);
        margin-right: 0;
    }
}
</style>