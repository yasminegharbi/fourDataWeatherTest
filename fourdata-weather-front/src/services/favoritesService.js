import { http } from './http'

export const favoritesService = {
  list() {
    return http.get('/favorites').then(r => r.data)
  },
  addByCity(city) {
    return http.post('/favorites', { city }).then(r => r.data)
  },
  remove(id) {
    return http.delete(`/favorites/${id}`)
  },
}
