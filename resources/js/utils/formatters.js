export function formatPhone(value) {
    if (!value) {
        return '';
    }

    let digits = String(value).replace(/\D/g, '');

    if (digits.startsWith('55') && digits.length === 13) {
        digits = digits.slice(2);
    }

    if (digits.length === 11) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
    }

    if (digits.length === 10) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    }

    return value;
}
