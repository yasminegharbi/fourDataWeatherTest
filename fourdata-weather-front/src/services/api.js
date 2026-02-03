import { http } from './http'

export async function getWeather(params) {
  // params: { city } ou { lat, lon }
  const res = await http.get('/weather', { params })
  return res.data
}

export async function listFavorites() {
  const res = await http.get('/favorites')
  return res.data
}

export async function addFavorite(payload) {
  // payload: { city } ou { latitude, longitude }
  const res = await http.post('/favorites', payload)
  return res.data
}

export async function deleteFavorite(id) {
  await http.delete(`/favorites/${id}`)
}
