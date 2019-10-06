DROP TABLE IF EXISTS diagnoses;
DROP TABLE IF EXISTS pathologies;
DROP TABLE IF EXISTS pollution_srcs;
DROP TABLE IF EXISTS users;

/* DDL */

CREATE TABLE IF NOT EXISTS pathologies (
	id SERIAL PRIMARY KEY,
	name VARCHAR(64) NOT NULL	
);

CREATE TABLE IF NOT EXISTS diagnoses (
	id SERIAL PRIMARY KEY,
	date DATE NOT NULL,
	id_pathology INTEGER REFERENCES pathologies(id) ON DELETE RESTRICT,
	location GEOGRAPHY NOT NULL
);

CREATE TABLE IF NOT EXISTS pollution_srcs (
	id SERIAL PRIMARY KEY,
	name VARCHAR NOT NULL,
	location GEOGRAPHY NOT NULL,
	date_from DATE NOT NULL,
	date_to DATE
);

CREATE TABLE IF NOT EXISTS users (
	id SERIAL PRIMARY KEY,
	username VARCHAR(16) NOT NULL,
	password VARCHAR NOT NULL,
	power INTEGER DEFAULT 1
);

/* INDICI */
CREATE INDEX index_pollution_srcs ON pollution_srcs USING GIST ( location );
CREATE INDEX index_diagnoses ON diagnoses USING GIST ( location );

/**
 * Dopo la creazioni degli indici va lanciato il VACUUM in modo da aggiornare le statistiche ed ottimizzare lo spazio.
 * Dalla doc ufficiale:
 * The ANALYZE command asks PostgreSQL to traverse the table and update its internal statistics used for query plan estimation (query plan analysis will be discussed later). The VACUUM command asks PostgreSQL to reclaim any unused space in the table pages left by updates or deletes to records. The VACUUM ANALYZE command performs both these actions.
 */
VACUUM ANALYZE pollution_srcs;
VACUUM ANALYZE diagnoses;
/**
 * Recupera più spazio ma è bloccante sulla tabella.
 */
VACUUM FULL pollution_srcs;
VACUUM FULL diagnoses;
