-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  jeu. 04 mars 2021 à 17:59
-- Version du serveur :  10.1.28-MariaDB
-- Version de PHP :  7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`tlemaitre`@`%` PROCEDURE `ajoutPassager` (IN `idM` INT(50), IN `idT` INT(50))  NO SQL
BEGIN
DECLARE i tinyint(3);
set i= (SELECT nbPlacesRestantes FROM trajet WHERE idTrajet=idT);
	IF( 1 < i)
    THEN 
    INSERT INTO passager VALUES ((SELECT nbPlacesRestantes from trajet WHERE trajet.idTrajet=idT),idT,idM);
    set i=i-1;
    UPDATE trajet SET NbPlacesRestantes=i WHERE idTrajet=idT;
    ELSE
      SIGNAL SQLSTATE '45101'
        SET MESSAGE_TEXT = 'La voiture est déjà pleine.';
    END IF;
END$$

CREATE DEFINER=`tlemaitre`@`%` PROCEDURE `bannir` (IN `idM` INT(50))  NO SQL
BEGIN
	UPDATE membre set statut=2 WHERE idMembre=idM;
   delete from trajet WHERE idConducteur=idM;
END$$

CREATE DEFINER=`tlemaitre`@`%` PROCEDURE `estBanTempo` (IN `adrM` VARCHAR(50), OUT `d` DATE)  NO SQL
BEGIN
SELECT membre.DateInterdit into d FROM membre WHERE 			     membre.AdrMail=adrM;  
IF (d <= CURDATE()) THEN
    	UPDATE membre set DateInterdit=NULL where AdrMail=adrM;
    END if;
END$$

CREATE DEFINER=`tlemaitre`@`%` PROCEDURE `retraitPassager` (IN `idM` INT(50), IN `idT` INT(50))  NO SQL
BEGIN
DECLARE i tinyint(3);
DECLARE j tinyint(3);
set i= (SELECT nbPlacesRestantes FROM trajet WHERE idTrajet=idT);
set j= (SELECT nbPlace FROM trajet,vehicule WHERE vehicule.Immatriculation=trajet.Immatriculation AND idTrajet=idT);
	IF( i < j)
    THEN 
    DELETE FROM passager WHERE idtrajet=idT AND idmembre=idM; 
    set i=i+1;
    UPDATE trajet SET NbPlacesRestantes=i WHERE idTrajet=idT;
    ELSE
      SIGNAL SQLSTATE '45111'
        SET MESSAGE_TEXT = 'Il n\' a plus personne a enlever .';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `idMembre` int(50) NOT NULL,
  `AdrMail` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Nom` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Prenom` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `MotDP` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Age` tinyint(3) UNSIGNED NOT NULL,
  `Telephone` int(10) UNSIGNED NOT NULL,
  `Statut` tinyint(1) UNSIGNED NOT NULL,
  `Avis` int(10) UNSIGNED NOT NULL DEFAULT '5',
  `NbAvis` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `DateInterdit` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `membre`
--

INSERT INTO `membre` (`idMembre`, `AdrMail`, `Nom`, `Prenom`, `MotDP`, `Age`, `Telephone`, `Statut`, `Avis`, `NbAvis`, `DateInterdit`) VALUES
(1, 'hamza@gmail.com', 'hamza', 'vador', 'boug', 27, 123456789, 2, 0, 0, NULL),
(2, 'interdit@gmail.com', 'Pale', 'Bienvenue', '123', 80, 622334455, 0, 0, 5, '2054-12-20'),
(6, 'lemaitre34560@gmail.com', 'Lemaitre', 'Thomas', 'azer', 21, 612207054, 1, 25, 5, NULL),
(852, 'sqdfgh', '', '', '', 85, 123456789, 0, 25, 5, NULL),
(987654322, 'test2@gmail.com', 'test', '2', '123', 20, 1234567890, 0, 5, 1, NULL),
(987654323, 'test3@gmail.com', 'test', '3', '123', 20, 123456798, 0, 5, 1, NULL);

--
-- Déclencheurs `membre`
--
DELIMITER $$
CREATE TRIGGER `before_delete_membre` BEFORE DELETE ON `membre` FOR EACH ROW BEGIN
	DELETE FROM trajet WHERE trajet.idConducteur=OLD.idMembre;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_membre` BEFORE INSERT ON `membre` FOR EACH ROW BEGIN
    IF NEW.Age < 18
    OR NEW.Age > 100
    THEN
    SIGNAL SQLSTATE '45001'
        SET MESSAGE_TEXT = 'AGE INCORRECT.';
    END IF;
    IF NEW.AdrMail IN (SELECT AdrMail FROM membre) THEN
    	SIGNAL SQLSTATE '45002'
        SET MESSAGE_TEXT = 'Adresse Mail déjà existente.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `passager`
--

CREATE TABLE `passager` (
  `place` int(10) UNSIGNED DEFAULT NULL,
  `idtrajet` int(50) NOT NULL,
  `idMembre` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `passager`
--

INSERT INTO `passager` (`place`, `idtrajet`, `idMembre`) VALUES
(5, 0, 1),
(3, 123465790, 6);

-- --------------------------------------------------------

--
-- Structure de la table `trajet`
--

CREATE TABLE `trajet` (
  `idTrajet` int(11) NOT NULL,
  `DateDep` date NOT NULL,
  `Prix` tinyint(3) UNSIGNED NOT NULL,
  `Distance` int(10) UNSIGNED NOT NULL,
  `idConducteur` int(50) DEFAULT NULL,
  `Immatriculation` varchar(50) DEFAULT NULL,
  `adresseRdv` varchar(50) NOT NULL,
  `adresseArr` varchar(50) NOT NULL,
  `NbPlacesRestantes` tinyint(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `trajet`
--

INSERT INTO `trajet` (`idTrajet`, `DateDep`, `Prix`, `Distance`, `idConducteur`, `Immatriculation`, `adresseRdv`, `adresseArr`, `NbPlacesRestantes`) VALUES
(0, '2017-12-22', 30, 30, 6, 'AA123AA', '', '', 5),
(123465790, '2017-12-21', 20, 20, 6, 'DD746QF', '', '', 0),
(123465791, '2017-12-21', 50, 35, 6, 'TE123TE', '', '', 0),
(123465792, '2017-12-21', 5, 35, 6, 'AA123AA', '', '', 0),
(123465794, '2017-12-29', 30, 20, 6, 'AA123AA', '', '', 0);

--
-- Déclencheurs `trajet`
--
DELIMITER $$
CREATE TRIGGER `before_insert_trajet` BEFORE INSERT ON `trajet` FOR EACH ROW BEGIN
    IF NEW.DateDep < CURDATE() THEN
        SIGNAL SQLSTATE '45003'
        SET MESSAGE_TEXT = 'Date de départ incorrecte.';
    END IF;   
    IF New.Immatriculation NOT IN (SELECT Immatriculation FROM vehicule where New.IdConducteur=vehicule.IdMembre)
    THEN
            SIGNAL SQLSTATE '45011'
        SET MESSAGE_TEXT = 'Aucun véhicule renseigné.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `login` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`login`, `password`) VALUES
('deux', 'f83815aedaa1b6bf4211e85910e6bc82'),
('un', '0674272bac0715f803e382b5aa437e08');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `Immatriculation` varchar(50) NOT NULL,
  `Marque` varchar(20) NOT NULL,
  `Couleur` varchar(10) NOT NULL,
  `nbPlace` tinyint(3) UNSIGNED NOT NULL,
  `IdMembre` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`Immatriculation`, `Marque`, `Couleur`, `nbPlace`, `IdMembre`) VALUES
('AA123AA', 'Zastava ', 'Beige', 5, 6),
('DD746QF', 'Renault', 'Blanche', 5, 6),
('TE123TE', 'Ferrari', 'Rouge', 2, 6);

--
-- Déclencheurs `vehicule`
--
DELIMITER $$
CREATE TRIGGER `BEFORE_INSERT_vehicule` BEFORE INSERT ON `vehicule` FOR EACH ROW BEGIN
	IF New.NbPlace <=0 THEN
		  SIGNAL SQLSTATE '45010'
        SET MESSAGE_TEXT = 'Nombre de places insuffisantes.';  
    END if;
	IF 0 != (SELECT count(Immatriculation) FROM vehicule,membre WHERE membre.IdMembre=vehicule.IdMembre AND Immatriculation = New.Immatriculation AND vehicule.IdMembre=New.IdMembre )
    THEN  SIGNAL SQLSTATE '45005'
        SET MESSAGE_TEXT = 'Immatriculation déja existente.';
     END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

CREATE TABLE `ville` (
  `idVille` int(50) NOT NULL,
  `Nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `CodePostal` int(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `ville`
--

INSERT INTO `ville` (`idVille`, `Nom`, `CodePostal`) VALUES
(1, 'Montpellier, France', 34000),
(2, 'Paris, France', 75000),
(3, 'Villeveyrac, France', 34560),
(4, 'Perpignan, France', 66000),
(5, 'Frontignan, France', 34110),
(6, 'Fabregues, France', 34690);

--
-- Déclencheurs `ville`
--
DELIMITER $$
CREATE TRIGGER `Before_insert_ville` BEFORE INSERT ON `ville` FOR EACH ROW BEGIN
    IF NEW.Nom IN (SELECT Nom FROM ville GROUP BY Nom)
    THEN
    	 SIGNAL SQLSTATE '45004'
        SET MESSAGE_TEXT = 'Ville déjà existente.';
     END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `villeetape`
--

CREATE TABLE `villeetape` (
  `Ordre` tinyint(3) UNSIGNED NOT NULL,
  `idville` int(50) NOT NULL,
  `idTrajet` int(50) NOT NULL,
  `PrisePassager` tinyint(3) UNSIGNED NOT NULL,
  `DepotPassager` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `villeetape`
--

INSERT INTO `villeetape` (`Ordre`, `idville`, `idTrajet`, `PrisePassager`, `DepotPassager`) VALUES
(2, 1, 123465790, 0, 1),
(3, 3, 123465790, 0, 0),
(2, 3, 123465791, 0, 0),
(2, 3, 123465792, 0, 0),
(1, 4, 123465790, 1, 0),
(1, 4, 123465791, 0, 0),
(1, 4, 123465792, 0, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`idMembre`);

--
-- Index pour la table `passager`
--
ALTER TABLE `passager`
  ADD PRIMARY KEY (`idtrajet`,`idMembre`),
  ADD KEY `idMembreFK` (`idMembre`,`idtrajet`) USING BTREE;

--
-- Index pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD PRIMARY KEY (`idTrajet`) USING BTREE,
  ADD KEY `ImmatriculationFK` (`Immatriculation`),
  ADD KEY `idConducteur_FK` (`idConducteur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`login`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`Immatriculation`) USING BTREE,
  ADD KEY `idMembre_FK` (`IdMembre`);

--
-- Index pour la table `ville`
--
ALTER TABLE `ville`
  ADD PRIMARY KEY (`idVille`);

--
-- Index pour la table `villeetape`
--
ALTER TABLE `villeetape`
  ADD PRIMARY KEY (`idville`,`idTrajet`),
  ADD KEY `idTrajetFK` (`idTrajet`),
  ADD KEY `idVille_FK` (`idville`) USING BTREE;

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `idMembre` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=987654324;

--
-- AUTO_INCREMENT pour la table `trajet`
--
ALTER TABLE `trajet`
  MODIFY `idTrajet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123465795;

--
-- AUTO_INCREMENT pour la table `ville`
--
ALTER TABLE `ville`
  MODIFY `idVille` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `passager`
--
ALTER TABLE `passager`
  ADD CONSTRAINT `idMembreFK` FOREIGN KEY (`idMembre`) REFERENCES `membre` (`idMembre`),
  ADD CONSTRAINT `idtrajet_FK` FOREIGN KEY (`idtrajet`) REFERENCES `trajet` (`idTrajet`);

--
-- Contraintes pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD CONSTRAINT `ImmatriculationFK` FOREIGN KEY (`Immatriculation`) REFERENCES `vehicule` (`Immatriculation`),
  ADD CONSTRAINT `idConducteur_FK` FOREIGN KEY (`idConducteur`) REFERENCES `membre` (`idMembre`);

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `idMembre_FK` FOREIGN KEY (`IdMembre`) REFERENCES `membre` (`idMembre`);

--
-- Contraintes pour la table `villeetape`
--
ALTER TABLE `villeetape`
  ADD CONSTRAINT `idTrajetFK` FOREIGN KEY (`idTrajet`) REFERENCES `trajet` (`idTrajet`),
  ADD CONSTRAINT `idVille_FK` FOREIGN KEY (`idville`) REFERENCES `ville` (`idVille`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
