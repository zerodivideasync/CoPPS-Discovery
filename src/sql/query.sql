/**
 * Recuperare tutte le Diagnosi e Fonti di Inquinamento specificata un'area.
 * La prima query recupera tutte le Fonti di inquinamento mentre la seconda recupera tutte le diagnosi presenti 
 * all'interno di un cerchio con centro sul Vesuvio e raggio di 50km.
 */
SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape 
FROM pollution_srcs AS s 
WHERE ST_INTERSECTS(s.location, ST_SetSRID(ST_Buffer(ST_SetSRID(ST_MakePoint(14.430004950376, 40.831063763109), 4326)::geography, 50000 ), 4326)) ORDER BY ST_AREA(location) DESC;

SELECT d.id, d.date, d.id_pathology, p.name, ST_AsText(location::geography) AS shape 
FROM diagnoses AS d JOIN pathologies AS p ON p.id = d.id_pathology 
WHERE ST_INTERSECTS(d.location, ST_SetSRID(ST_Buffer(ST_SetSRID(ST_MakePoint(14.430004950376, 40.831063763109), 4326)::geography, 50000), 4326)) ORDER BY ST_AREA(d.location) DESC;

/**
 * La prima query recupera tutte le Fonti di inquinamento presenti all'interno di un poligono. L'area in questione è Napoli. È presente un filtro per la data.
 * La seconda query recupera tutte le Diagnosi presenti nella medesima area.
 */
 SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape 
 FROM pollution_srcs AS s 
 WHERE ST_INTERSECTS(s.location, ST_SetSRID(ST_MakePolygon(ST_MakeLine(ARRAY[ST_SetSRID(ST_MakePoint(13.607403632017, 40.813396916684), 4326),ST_SetSRID(ST_MakePoint(13.986431952329, 40.367058671002), 4326),ST_SetSRID(ST_MakePoint(15.024639960142, 40.775969238434), 4326),ST_SetSRID(ST_MakePoint(14.327008124204, 41.277397050554), 4326),ST_SetSRID(ST_MakePoint(13.607403632017, 40.813396916684), 4326)])), 4326)) 
 AND date_from >= '2018/01/01' ORDER BY ST_AREA(location) DESC;
 
 SELECT d.id, d.date, d.id_pathology, p.name, ST_AsText(location::geography) AS shape 
 FROM diagnoses AS d JOIN pathologies AS p ON p.id = d.id_pathology 
 WHERE ST_INTERSECTS(d.location, ST_SetSRID(ST_MakePolygon(ST_MakeLine(ARRAY[ST_SetSRID(ST_MakePoint(13.607403632017, 40.813396916684), 4326),ST_SetSRID(ST_MakePoint(13.986431952329, 40.367058671002), 4326),ST_SetSRID(ST_MakePoint(15.024639960142, 40.775969238434), 4326),ST_SetSRID(ST_MakePoint(14.327008124204, 41.277397050554), 4326),ST_SetSRID(ST_MakePoint(13.607403632017, 40.813396916684), 4326)])), 4326)) 
 AND d.date >= '2018/01/01' ORDER BY ST_AREA(d.location) DESC;

/**
 * Recuperare tutte le Fonti di Inquinamento data una Diagnosi, una distanza da essa ed una data
 */
SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape 
FROM pollution_srcs 
WHERE ST_DWithin(location, 'POINT(14.437481534766 40.856860975905)'::geography, 50000) AND date_from >= '2017/12/01' ;


/**
 * Recuperare informazioni sulle Patologie più frequenti data una Fonte di Inquinamento, una distanza da essa ed una data
 */
SELECT d.id, d.date, ST_AsText(d.location::geography) AS shape, pathologies_occurrences.* 
FROM diagnoses AS d JOIN 
    ( 
	SELECT p.id as id_pathology, p.name, COUNT(*) AS pathology_occurrence 
	FROM diagnoses AS t JOIN pathologies AS p ON id_pathology = p.id 
	WHERE ST_DWithin(t.location, 'POINT(14.174180019348 40.801665579168)'::geography, 60000) AND t.date >= '2018/02/01' AND t.date <= '2018/04/18' 
	GROUP BY p.id 
	) as pathologies_occurrences ON d.id_pathology = pathologies_occurrences.id_pathology 
WHERE ST_DWithin(d.location, 'POINT(14.174180019348 40.801665579168)'::geography, 60000) AND d.date >= '2018/02/01' AND d.date <= '2018/04/18' 
ORDER BY pathologies_occurrences.pathology_occurrence DESC, pathologies_occurrences.id_pathology ASC