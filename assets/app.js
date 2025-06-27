import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


// Désactiver le cache de Turbo si nécessaire
document.addEventListener('turbo:load', () => {
    // Initialisation globale si nécessaire
    console.log('Page chargée avec Turbo');
});