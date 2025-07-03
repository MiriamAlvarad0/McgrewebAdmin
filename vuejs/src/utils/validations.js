export const containsUser = (value) => {
    return value.includes('bsl');
};

export const upperCase = (value) => {
    return /(?=.*[A-Z])/.test(value);
};

export const specialCharacter = (value) => {
    return /([!@#$%^&*])/.test(value);
};

export const numberCharacter = (value) => {
    return /(?=.*\d)/.test(value);
};

export const spaces = (value) => {
    return (value || '').indexOf(' ') < 0;
};
