import { http } from './http'

export const weatherService = {
  byCity(city) {
    return http.get('/weather', { params: { city } }).then(r => r.data)
  },
  byCoords(lat, lon) {
    return http.get('/weather', { params: { lat, lon } }).then(r => r.data)
  },
}
