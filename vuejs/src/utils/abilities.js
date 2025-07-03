import { ref } from 'vue'
import axios from 'axios'
import { setEncryptedTimeLimit, hasTimeLimitExpired, setEncrypted, getDecrypted } from './encryption'

const productionBaseURL = import.meta.env.VITE_productionBaseURL || window.location.origin
const localBaseURL = import.meta.env.VITE_localBaseURL
const baseURL = window.location.hostname === 'localhost' ? localBaseURL : productionBaseURL;
axios.defaults.baseURL = baseURL;

//export const permission = reactive({
    //ability: 'user:delete' //Default Permission (only to test)
//})

const abilities = ref('')
const hasExpired = ref(false)

export async function definedAbilities(){
    hasExpired.value = hasTimeLimitExpired();
    if(hasExpired.value || !localStorage.getItem('abilities')){
        const token = ref(localStorage.getItem('auth_token'))
        if(token.value){
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
            const res = await axios.get('/api/v1/auth/abilities')
            if(res){
                const abilitiesString = res.data.join(',');
                localStorage.setItem('abilities', setEncrypted(abilitiesString));
                if(localStorage.getItem('abilities')){
                    abilities.value = getDecrypted(localStorage.getItem('abilities')).split(',')
                }
            } 
        }
        setEncryptedTimeLimit(5) //5 minutos
    }
}

if(localStorage.getItem('abilities')){
    abilities.value = getDecrypted(localStorage.getItem('abilities')).split(',')
}

export const functions = {
    install: (app) => {
        app.config.globalProperties.can = function(value){
            if(abilities.value == null || abilities.value == ''){
                return false
            }
            let permissions = abilities.value
            let _return = false
            if(value.includes('|')){
                value.split('|').forEach(function (item) {
                    if(permissions.includes(item.trim())){
                        _return = true
                    }
                })
            }else if(value.includes('&')){
                value.split('&').forEach(function (item) {
                    if(permissions.includes(item.trim())){
                        _return = true
                    }
                })
            }else{
                _return = permissions.includes(value.trim())
            }
            return _return
        }
    }
}
