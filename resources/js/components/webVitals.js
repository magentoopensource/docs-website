import { onCLS, onFID, onLCP, onFCP, onTTFB, onINP } from 'web-vitals';

/**
 * Send Web Vitals metrics to Google Tag Manager
 * Tracks Core Web Vitals (CLS, FID/INP, LCP) plus FCP and TTFB
 */
function sendToGTM({ name, value, rating, delta, id }) {
    // Check if GTM dataLayer exists
    if (typeof window.dataLayer !== 'undefined') {
        window.dataLayer.push({
            event: 'web-vitals',
            event_category: 'Web Vitals',
            event_action: name,
            event_value: Math.round(name === 'CLS' ? value * 1000 : value), // CLS is decimal, convert to whole number
            event_label: id,
            metric_rating: rating, // 'good', 'needs-improvement', or 'poor'
            metric_delta: Math.round(delta),
        });

        // Log to console in development
        if (import.meta.env.DEV) {
            console.log(`[Web Vitals] ${name}:`, {
                value: Math.round(value),
                rating,
                delta: Math.round(delta),
            });
        }
    }
}

/**
 * Initialize Web Vitals tracking
 * Call this once when the page loads
 */
export function initWebVitals() {
    // Core Web Vitals
    onCLS(sendToGTM);   // Cumulative Layout Shift
    onLCP(sendToGTM);   // Largest Contentful Paint

    // FID is deprecated in favor of INP, but we'll track both for transition period
    onFID(sendToGTM);   // First Input Delay (legacy)
    onINP(sendToGTM);   // Interaction to Next Paint (new standard)

    // Other important metrics
    onFCP(sendToGTM);   // First Contentful Paint
    onTTFB(sendToGTM);  // Time to First Byte
}
