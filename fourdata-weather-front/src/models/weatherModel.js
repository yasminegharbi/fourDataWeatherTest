export const toWeatherViewModel = (raw, city = '—') => {
  if (!raw) return null
  const c = raw.current || raw.current_weather || {}
  const d = raw.daily || {}, t = d.time || [], min = d.temperature_2m_min || d.temp_min || [], max = d.temperature_2m_max || d.temp_max || [], p = d.precipitation_sum || []
  return {
    location: raw.city || raw.location || city,
    temp: c.temperature ?? raw.temperature ?? '—',
    wind: c.windspeed ?? raw.windSpeed ?? '—',
    precipitationToday: p[0] ?? 0,
    lat: raw.latitude ?? raw.lat ?? '—',
    lon: raw.longitude ?? raw.lon ?? '—',
    daily: t.map((x,i)=>({ date:x, label:new Date(x).toLocaleDateString(), min:min[i]??'—', max:max[i]??'—', precipitation:p[i]??0 })),
  }
}
