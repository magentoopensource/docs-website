/**
 * Frontend Error Logging
 * Captures JavaScript errors and sends them to GTM dataLayer
 * Can be configured to forward to Sentry, LogRocket, or other services via GTM
 */

/**
 * Send error to GTM dataLayer
 */
function sendErrorToGTM(errorData) {
    if (typeof window.dataLayer !== 'undefined') {
        window.dataLayer.push({
            event: 'javascript_error',
            error_message: errorData.message,
            error_stack: errorData.stack,
            error_filename: errorData.filename,
            error_lineno: errorData.lineno,
            error_colno: errorData.colno,
            error_url: window.location.href,
            error_user_agent: navigator.userAgent,
        });

        // Log to console in development
        if (import.meta.env.DEV) {
            console.error('[Error Logging]', errorData);
        }
    }
}

/**
 * Handle global JavaScript errors
 */
function handleError(event) {
    const errorData = {
        message: event.message || 'Unknown error',
        stack: event.error?.stack || '',
        filename: event.filename || '',
        lineno: event.lineno || 0,
        colno: event.colno || 0,
        timestamp: new Date().toISOString(),
    };

    sendErrorToGTM(errorData);
}

/**
 * Handle unhandled promise rejections
 */
function handleUnhandledRejection(event) {
    const errorData = {
        message: event.reason?.message || event.reason || 'Unhandled Promise Rejection',
        stack: event.reason?.stack || '',
        filename: event.reason?.fileName || '',
        lineno: event.reason?.lineNumber || 0,
        colno: event.reason?.columnNumber || 0,
        timestamp: new Date().toISOString(),
    };

    sendErrorToGTM(errorData);
}

/**
 * Initialize error logging
 */
export function initErrorLogging() {
    // Capture uncaught errors
    window.addEventListener('error', handleError, true);

    // Capture unhandled promise rejections
    window.addEventListener('unhandledrejection', handleUnhandledRejection, true);

    // Log that error logging is active
    if (import.meta.env.DEV) {
        console.log('[Error Logging] Initialized - errors will be sent to GTM dataLayer');
    }
}

/**
 * Manual error logging function
 * Use this to manually log errors in try/catch blocks
 *
 * @example
 * try {
 *   somethingRisky();
 * } catch (error) {
 *   logError(error, { context: 'payment-processing' });
 * }
 */
export function logError(error, additionalData = {}) {
    const errorData = {
        message: error.message || 'Manual error log',
        stack: error.stack || '',
        filename: error.fileName || '',
        lineno: error.lineNumber || 0,
        colno: error.columnNumber || 0,
        timestamp: new Date().toISOString(),
        ...additionalData,
    };

    sendErrorToGTM(errorData);
}

// Export to window for global access if needed
if (typeof window !== 'undefined') {
    window.logError = logError;
}
