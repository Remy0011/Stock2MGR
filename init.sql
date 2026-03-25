CREATE TABLE Entrepot(
   id_entrepot INT,
   num�ro_entrepot INT NOT NULL,
   date_entrepot VARCHAR(50) ,
   PRIMARY KEY(id_entrepot)
);

CREATE TABLE LOT(
   id_article INT,
   id_entrepot INT,
   id_lot INT,
   date_lot VARCHAR(50) ,
   quantit�_lot INT,
   id_entrepot_1 INT NOT NULL,
   PRIMARY KEY(id_article, id_entrepot, id_lot),
   FOREIGN KEY(id_entrepot_1) REFERENCES Entrepot(id_entrepot)
);

CREATE TABLE Role(
   id_role INT,
   nom_role VARCHAR(50) ,
   PRIMARY KEY(id_role)
);

CREATE TABLE Status(
   id_status INT,
   nom_status VARCHAR(50) ,
   PRIMARY KEY(id_status)
);

CREATE TABLE Type(
   id_type INT,
   nom_type VARCHAR(50) ,
   PRIMARY KEY(id_type)
);

CREATE TABLE Utilisateur(
   Id_utilisateur INT,
   mail_utilisateur VARCHAR(50) ,
   mdp_utilisateur VARCHAR(50) ,
   Role_utilisateur INT,
   id_role INT NOT NULL,
   PRIMARY KEY(Id_utilisateur),
   FOREIGN KEY(id_role) REFERENCES Role(id_role)
);

CREATE TABLE Article(
   id_article INT,
   id_status INT,
   nom_article VARCHAR(50) ,
   seuil_article VARCHAR(50) ,
   id_status_1 INT NOT NULL,
   PRIMARY KEY(id_article, id_status),
   FOREIGN KEY(id_status_1) REFERENCES Status(id_status)
);

CREATE TABLE Mouvement(
   id_mouvement INT,
   id_entrepot_source INT,
   id_entrepot_destination INT,
   id_utilisateur INT,
   id_type INT,
   id_article INT,
   quantit�_mouvement INT,
   date_mouvement VARCHAR(50) ,
   id_type_1 INT NOT NULL,
   PRIMARY KEY(id_mouvement, id_entrepot_source, id_entrepot_destination, id_utilisateur, id_type, id_article),
   FOREIGN KEY(id_type_1) REFERENCES Type(id_type)
);

CREATE TABLE Stock(
   id_stock INT,
   id_article INT,
   id_entrepot INT,
   Quantit�_stock INT,
   id_article_1 INT NOT NULL,
   id_status INT NOT NULL,
   PRIMARY KEY(id_stock, id_article, id_entrepot),
   FOREIGN KEY(id_article_1, id_status) REFERENCES Article(id_article, id_status)
);

CREATE TABLE Asso_2(
   id_entrepot INT,
   id_stock INT,
   id_article INT,
   id_entrepot_1 INT,
   PRIMARY KEY(id_entrepot, id_stock, id_article, id_entrepot_1),
   FOREIGN KEY(id_entrepot) REFERENCES Entrepot(id_entrepot),
   FOREIGN KEY(id_stock, id_article, id_entrepot_1) REFERENCES Stock(id_stock, id_article, id_entrepot)
);

CREATE TABLE Asso_3(
   id_article INT,
   id_status INT,
   id_mouvement INT,
   id_entrepot_source INT,
   id_entrepot_destination INT,
   id_utilisateur INT,
   id_type INT,
   id_article_1 INT,
   PRIMARY KEY(id_article, id_status, id_mouvement, id_entrepot_source, id_entrepot_destination, id_utilisateur, id_type, id_article_1),
   FOREIGN KEY(id_article, id_status) REFERENCES Article(id_article, id_status),
   FOREIGN KEY(id_mouvement, id_entrepot_source, id_entrepot_destination, id_utilisateur, id_type, id_article_1) REFERENCES Mouvement(id_mouvement, id_entrepot_source, id_entrepot_destination, id_utilisateur, id_type, id_article)
);

CREATE TABLE Asso_4(
   Id_utilisateur INT,
   id_mouvement INT,
   id_entrepot_source INT,
   id_entrepot_destination INT,
   id_utilisateur_1 INT,
   id_type INT,
   id_article INT,
   PRIMARY KEY(Id_utilisateur, id_mouvement, id_entrepot_source, id_entrepot_destination, id_utilisateur_1, id_type, id_article),
   FOREIGN KEY(Id_utilisateur) REFERENCES Utilisateur(Id_utilisateur),
   FOREIGN KEY(id_mouvement, id_entrepot_source, id_entrepot_destination, id_utilisateur_1, id_type, id_article) REFERENCES Mouvement(id_mouvement, id_entrepot_source, id_entrepot_destination, id_utilisateur, id_type, id_article)
);

CREATE TABLE Asso_9(
   id_article INT,
   id_status INT,
   id_article_1 INT,
   id_entrepot INT,
   id_lot INT,
   PRIMARY KEY(id_article, id_status, id_article_1, id_entrepot, id_lot),
   FOREIGN KEY(id_article, id_status) REFERENCES Article(id_article, id_status),
   FOREIGN KEY(id_article_1, id_entrepot, id_lot) REFERENCES LOT(id_article, id_entrepot, id_lot)
);
