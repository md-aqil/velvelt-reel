# Global JavaScript Functionality

This document explains how to use the global JavaScript functionality added to the theme.

## Files

- `global.js` - Contains global JavaScript functions and components
- `global.css` - Contains CSS for global components like notifications and modals

## Features

### 1. Notifications

Display notifications to users with different types:

```javascript
// Success notification
showNotification('Operation completed successfully', 'success');

// Error notification
showNotification('An error occurred', 'error');

// Warning notification
showNotification('Please check your input', 'warning');

// Info notification
showNotification('This is an informational message', 'info');
```

### 2. AJAX Requests

Make AJAX requests with WordPress nonce security:

```javascript
ajaxRequest('/wp-admin/admin-ajax.php', { 
    action: 'my_action',
    data: 'some_data'
})
.then(response => {
    console.log('Success:', response);
})
.catch(error => {
    console.error('Error:', error);
});
```

### 3. File Upload Previews

Automatic preview of selected files in file input areas with class `file-upload-area`.

### 4. Form Enhancements

- Automatic loading states for submit buttons
- Confirmation dialogs for forms with `data-confirm` attribute

### 5. Mobile Menu Toggle

Automatic handling of mobile menu toggle functionality.

### 6. Modals

Simple modal functionality with data attributes:
- `data-modal-trigger="modal-id"` to open a modal
- `data-modal-close` to close a modal

### 7. Tooltips

Simple tooltip functionality with `data-tooltip` attribute.

## Usage

The global.js file is automatically enqueued on all pages through functions.php. No additional steps are needed to use it.

## Customization

To add new global functionality:

1. Add your functions to global.js
2. Call initialization functions in `initializeGlobalScripts()`
3. Add corresponding CSS to global.css if needed

## Testing

Open `test-global-js.html` in a browser to test the functionality.