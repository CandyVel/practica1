async function cargarClima() {
    try {
        const res = await fetch(
            'https://api.open-meteo.com/v1/forecast?' +
            'latitude=17.0732&longitude=-96.7266' +
            '&current=temperature_2m,windspeed_10m,precipitation,weathercode' +
            '&timezone=America%2FMexico_City'
        );
        const data = await res.json();
        const c    = data.current;

        const temp   = c.temperature_2m;
        const viento = c.windspeed_10m;
        const lluvia = c.precipitation;
        const codigo = c.weathercode;

        function interpretarClima(code) {
            if (code === 0)  return { icono: 'bi-sun',              texto: 'Despejado',            color: '#fdcb6e' };
            if (code <= 3)   return { icono: 'bi-cloud-sun',        texto: 'Parcialmente nublado', color: '#74b9ff' };
            if (code <= 49)  return { icono: 'bi-cloud',            texto: 'Nublado',              color: '#636e72' };
            if (code <= 67)  return { icono: 'bi-cloud-rain',       texto: 'Lluvia',               color: '#0984e3' };
            if (code <= 77)  return { icono: 'bi-cloud-snow',       texto: 'Nieve',                color: '#74b9ff' };
            if (code <= 82)  return { icono: 'bi-cloud-drizzle',    texto: 'Llovizna',             color: '#0984e3' };
            return                  { icono: 'bi-lightning-charge', texto: 'Tormenta',             color: '#6c5ce7' };
        }

        function recomendacion(temp, viento, lluvia) {
            if (lluvia > 0)  return { msg: 'No es buen dia para ejercitarte, hay lluvia.',      color: '#d63031' };
            if (viento > 30) return { msg: 'Viento fuerte, ten cuidado en la carretera.', color: '#e17055' };
            if (temp > 35)   return { msg: 'Mucho calor, hidratate bien antes de salir.', color: '#e17055' };
            if (temp < 10)   return { msg: 'Hace frio, abrigate bien para ejercitarte.',        color: '#74b9ff' };
            return                  { msg: 'Condiciones ideales para ejercitarte.',             color: '#00b894' };
        }

        const clima = interpretarClima(codigo);
        const rec   = recomendacion(temp, viento, lluvia);

        document.getElementById('climaWidget').innerHTML = `
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi ${clima.icono} fs-3" style="color:${clima.color}"></i>
                <div>
                    <div class="fw-bold fs-5">${temp}°C</div>
                    <div class="small text-muted">${clima.texto}</div>
                </div>
            </div>
            <ul class="club-list mb-2" style="font-size:0.82rem">
                <li>Viento: <strong>${viento} km/h</strong></li>
                <li>Lluvia: <strong>${lluvia} mm</strong></li>
            </ul>
            <div class="small p-2 rounded" style="background:#f8f9fa;border-left:3px solid ${rec.color};color:${rec.color}">
                <i class=""></i>${rec.msg}
            </div>`;

    } catch(e) {
        document.getElementById('climaWidget').innerHTML =
            '<p class="small text-muted">No se pudo cargar el clima.</p>';
    }
}

cargarClima();