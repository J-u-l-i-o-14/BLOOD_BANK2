
@extends('layouts.public')

@section('title', 'Confirmation de rendez-vous')

@push('styles')
<style>
    @keyframes checkmark {
        0% {
            stroke-dashoffset: 100;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }
    
    @keyframes fadeInScale {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes confetti {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
    
    .checkmark-circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #10b981;
        fill: none;
        animation: checkmark 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    
    .checkmark {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: block;
        stroke-width: 3;
        stroke: #10b981;
        stroke-miterlimit: 10;
        box-shadow: inset 0px 0px 0px #10b981;
        animation: fadeInScale 0.8s ease-in-out 0.3s both;
    }
    
    .checkmark-check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: checkmark 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }
    
    .confetti-piece {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #dc2626;
        animation: confetti 3s linear infinite;
    }
    
    .confetti-piece:nth-child(1) { left: 10%; animation-delay: 0s; background: #dc2626; }
    .confetti-piece:nth-child(2) { left: 20%; animation-delay: 0.5s; background: #10b981; }
    .confetti-piece:nth-child(3) { left: 30%; animation-delay: 1s; background: #3b82f6; }
    .confetti-piece:nth-child(4) { left: 40%; animation-delay: 1.5s; background: #f59e0b; }
    .confetti-piece:nth-child(5) { left: 50%; animation-delay: 2s; background: #8b5cf6; }
    .confetti-piece:nth-child(6) { left: 60%; animation-delay: 0.3s; background: #ef4444; }
    .confetti-piece:nth-child(7) { left: 70%; animation-delay: 0.8s; background: #06b6d4; }
    .confetti-piece:nth-child(8) { left: 80%; animation-delay: 1.3s; background: #84cc16; }
    .confetti-piece:nth-child(9) { left: 90%; animation-delay: 1.8s; background: #f97316; }
    
    .success-gradient {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<!-- Confetti Animation -->
<div class="fixed inset-0 pointer-events-none z-10">
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
    <div class="confetti-piece"></div>
</div>

<!-- Success Section -->
<section class="success-gradient py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="max-w-2xl mx-auto">
            <!-- Success Icon -->
            <div class="mb-8">
                <svg class="checkmark mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark-check" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Rendez-vous confirmé !
            </h1>
            <p class="text-xl text-green-100 mb-8">
                Merci pour votre engagement. Votre geste peut sauver des vies.
            </p>
            
            <div class="bg-white bg-opacity-20 backdrop-filter backdrop-blur-lg rounded-2xl p-6 text-white">
                <h3 class="text-lg font-semibold mb-2">
                    <i class="fas fa-envelope mr-2"></i>
                    Confirmation envoyée
                </h3>
                <p class="text-green-100">
                    Un email de confirmation a été envoyé à votre adresse avec tous les détails.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Appointment Details -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Détails de votre rendez-vous
                </h2>
                <p class="text-gray-600">
                    Voici un récapitulatif de votre rendez-vous. Conservez ces informations.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Appointment Info -->
                <div class="info-card rounded-2xl p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-calendar-check text-green-600 mr-3"></i>
                        Informations du rendez-vous
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-gray-600">
                                <i class="fas fa-user mr-2"></i>
                                Nom
                            </span>
                            <span class="font-semibold text-gray-900">{{ $appointment->name ?? 'John Doe' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-gray-600">
                                <i class="fas fa-envelope mr-2"></i>
                                Email
                            </span>
                            <span class="font-semibold text-gray-900">{{ $appointment->email ?? 'john@example.com' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-gray-600">
                                <i class="fas fa-calendar mr-2"></i>
                                Date
                            </span>
                            <span class="font-semibold text-gray-900">{{ $appointment->preferred_date ?? '2024-01-15' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-200">
                            <span class="text-gray-600">
                                <i class="fas fa-clock mr-2"></i>
                                Heure
                            </span>
                            <span class="font-semibold text-gray-900">{{ $appointment->preferred_time ?? '10:00' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3">
                            <span class="text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Lieu
                            </span>
                            <span class="font-semibold text-gray-900">{{ $appointment->campaign->location ?? 'Centre de don principal' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="info-card rounded-2xl p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-list-check text-blue-600 mr-3"></i>
                        Prochaines étapes
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Confirmation reçue</h4>
                                <p class="text-gray-600 text-sm">Votre rendez-vous est confirmé</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-blue-600 text-sm font-bold">2</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Préparez-vous</h4>
                                <p class="text-gray-600 text-sm">Suivez nos recommandations avant le don</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-yellow-600 text-sm font-bold">3</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Rappel automatique</h4>
                                <p class="text-gray-600 text-sm">Vous recevrez un rappel 24h avant</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                <span class="text-red-600 text-sm font-bold">4</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Jour J</h4>
                                <p class="text-gray-600 text-sm">Présentez-vous 15 min avant l'heure</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Preparation Tips -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Comment bien se préparer ?
                </h2>
                <p class="text-gray-600">
                    Suivez ces conseils pour que votre don se passe dans les meilleures conditions.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Before -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8">
                    <h3 class="text-xl font-bold text-blue-900 mb-6 flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-3"></i>
                        Avant le don
                    </h3>
                    
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-3"></i>
                            <span class="text-blue-800">Dormez bien la nuit précédente</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-3"></i>
                            <span class="text-blue-800">Prenez un petit-déjeuner consistant</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-3"></i>
                            <span class="text-blue-800">Buvez beaucoup d'eau</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-3"></i>
                            <span class="text-blue-800">Évitez l'alcool 24h avant</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-3"></i>
                            <span class="text-blue-800">Apportez une pièce d'identité</span>
                        </li>
                    </ul>
                </div>

                <!-- After -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8">
                    <h3 class="text-xl font-bold text-green-900 mb-6 flex items-center">
                        <i class="fas fa-heart text-green-600 mr-3"></i>
                        Après le don
                    </h3>
                    
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                            <span class="text-green-800">Reposez-vous 10-15 minutes</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                            <span class="text-green-800">Buvez des liquides</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                            <span class="text-green-800">Évitez les efforts intenses</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                            <span class="text-green-800">Gardez le pansement 4h</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                            <span class="text-green-800">Prenez une collation</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Action Buttons -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">
                Que souhaitez-vous faire maintenant ?
            </h2>
            
            <div class="grid sm:grid-cols-2 gap-4">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-home mr-2"></i>
                    Retour à l'accueil
                </a>
                
                <a href="{{ route('appointment.public') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Nouveau rendez-vous
                </a>
            </div>
            
            <div class="mt-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="font-semibold text-yellow-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Besoin de modifier votre rendez-vous ?
                </h3>
                <p class="text-yellow-700 text-sm">
                    Contactez-nous au <strong>+228 90082081</strong> ou par email à 
                    <strong>LifeSaver@gmail.com</strong>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Remove confetti after animation
    setTimeout(() => {
        document.querySelectorAll('.confetti-piece').forEach(piece => {
            piece.style.display = 'none';
        });
    }, 5000);

    // Add print functionality
    function printAppointment() {
        window.print();
    }

    // Add to calendar functionality
    function addToCalendar() {
        const title = 'Rendez-vous don de sang - LifeSaver';
        const details = 'Rendez-vous pour don de sang au centre LifeSaver';
        const location = '{{ $appointment->campaign->location ?? "Centre de don principal" }}';
        const startDate = '{{ $appointment->preferred_date ?? "2024-01-15" }}T{{ $appointment->preferred_time ?? "10:00" }}:00';
        
        const googleCalendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${startDate.replace(/[-:]/g, '')}/${startDate.replace(/[-:]/g, '')}&details=${encodeURIComponent(details)}&location=${encodeURIComponent(location)}`;
        
        window.open(googleCalendarUrl, '_blank');
    }

    // Smooth scroll to sections
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
@endpush