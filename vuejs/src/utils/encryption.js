import CryptoJS from 'crypto-js';

// Llave de encriptación (debe ser secreta y segura)
const secretKey = 'nueva-llavesita';

// Función para establecer el tiempo límite encriptado
export function setEncryptedTimeLimit(minutes) {
    const now = new Date();
    const timeLimit = new Date(now.getTime() + minutes * 60000);
    const encryptedTimeLimit = CryptoJS.AES.encrypt(timeLimit.toISOString(), secretKey).toString();
    localStorage.setItem('encryptedTimeLimit', encryptedTimeLimit);
}

// Función para obtener el tiempo límite desencriptado
export function getDecryptedTimeLimit() {
    const encryptedTimeLimit = localStorage.getItem('encryptedTimeLimit');
    if (!encryptedTimeLimit) {
        return null;
    }
    const bytes = CryptoJS.AES.decrypt(encryptedTimeLimit, secretKey);
    const decryptedTimeLimit = bytes.toString(CryptoJS.enc.Utf8);
    return new Date(decryptedTimeLimit);
}

// Función para verificar si el tiempo límite ha expirado
export function hasTimeLimitExpired() {
    const timeLimit = getDecryptedTimeLimit();
    if (!timeLimit) {
        return true; // Si no hay tiempo límite, considerarlo expirado
    }
    const now = new Date();
    return now > timeLimit;
}

// Función para encriptado
export function setEncrypted(value) {
    const encrypted = CryptoJS.AES.encrypt(value, secretKey)//.toString()
    return encrypted
}

// Función para obtener el tiempo límite desencriptado
export function getDecrypted(value) {
    const encrypted = value
    if (!encrypted) {
        return null
    }
    const bytes = CryptoJS.AES.decrypt(value, secretKey)
    const decrypted = bytes.toString(CryptoJS.enc.Utf8)
    return decrypted
}
//npm install crypto-js
