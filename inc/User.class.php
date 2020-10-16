<?php
class User {
	/**
	 * Constructeur
	 *
	 * @param string $phone Numéro de téléphone de l'utilisateur
	 */
	public function __construct(string $phone) {
		$this->phone = $phone;
	}
	
	/**
	 * Vérifie si l'utilisateur existe
	 *
	 * @return bool Résultat
	 */
	public function exists() : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] > 0;
	}
	
	/**
	 * Crée un utilisateur
	 *
	 * @param string $phone Numéro de téléphone
	 * @param string $password Mot de passe
	 * @param string $firstName Prénom
	 * @param string $lastName Nom
	 * @param string $companyName Nom de l'entreprise
	 * @param string $address1 Adresse 1
	 * @param string $address2 Adresse 2
	 * @param string $city Ville
	 * @param string $postalCode Code postal
	 * @param string $country Pays
	 *
	 * @return bool ID de l'utilisateur créé
	 */
	public static function create(string $phone, string $password, string $firstName, string $lastName, string $companyName, string $address1, string $address2, string $city, string $postalCode, string $country) {
		global $db;
		
		$query = $db->prepare("INSERT INTO users(phone, password, first_name, last_name, company_name, address1, address2, city, postal_code, country) VALUES(:phone, :password, :first_name, :last_name, :company_name, :address1, :address2, :city, :postal_code, :country)");
		$query->bindValue(":phone", $phone, PDO::PARAM_STR);
		$query->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
		$query->bindValue(":first_name", $firstName, PDO::PARAM_STR);
		$query->bindValue(":last_name", $lastName, PDO::PARAM_STR);
		$query->bindValue(":company_name", $companyName, PDO::PARAM_STR);
		$query->bindValue(":address1", $address1, PDO::PARAM_STR);
		$query->bindValue(":address2", $address2, PDO::PARAM_STR);
		$query->bindValue(":city", $city, PDO::PARAM_STR);
		$query->bindValue(":postal_code", $postalCode, PDO::PARAM_STR);
		$query->bindValue(":country", $country, PDO::PARAM_STR);
		$query->execute();
		
		return $db->lastInsertId();
	}
	
	/**
	 * Charge le profil de l'utilisateur
	 *
	 * @return array Profil de l'utilisateur
	 */
	public function getProfile() : array {
		global $db;
		
		$query = $db->prepare("SELECT phone, password, first_name, last_name, company_name, address1, address2, city, postal_code, country, has2fa, admin FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return array_map("trim", $data);
	}
	
	/**
	 * Vérifie si l'utilisateur a l'auth 2FA activée
	 *
	 * @return bool Résultat
	 */
	public function has2fa() : bool {
		global $db;
		
		$query = $db->prepare("SELECT 2fa FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["2fa"] == 1;
	}
	
	/**
	 * Envoie un code SMS
	 *
	 * @return bool Résultat
	 */
	public function sendSmsCode() : bool {
		global $config, $db, $dev;
		
		$code = random_int(1000000000, 9999999999);
		
		$query = $db->prepare("UPDATE users SET validation_code = :validation_code WHERE phone = :phone");
		$query->bindValue(":validation_code", $code, PDO::PARAM_INT);
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		
		if (!$dev) {
			$sms = new SMS($config["bulksms"]["token_id"], $config["bulksms"]["token_secret"]);
			return $sms->send("+33".substr($this->phone, 1), "Votre code de validation Serveur.tech est : $code");
		} else {
			return true;
		}
	}
	
	/**
	 * Récupère le code de validation SMS
	 *
	 * @return int Code de validation SMS
	 */
	public function getValidationCode() : int {
		global $db;
		
		$query = $db->prepare("SELECT validation_code FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch();
		
		return $data["validation_code"];
	}
	
	/**
	 * Vérifie le mot de passe de l'utilisateur
	 *
	 * @param string $password Mot de passe
	 *
	 * @param bool Résultat
	 */
	public function verifyPassword(string $password) : bool {
		global $db;
		
		$query = $db->prepare("SELECT password FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		if (!isset($data["password"])) {
			return false;
		}
		
		return password_verify($password, trim($data["password"]));
	}
	
	/**
	 * Modifie le mot de passe de l'utilisateur
	 *
	 * @param string $password Nouveau mot de passe
	 *
	 * @return bool Résultat
	 
	 */
	public function changePassword(string $password) : bool {
		global $db;
		
		$query = $db->prepare("UPDATE users SET password = :password WHERE phone = :phone");
		$query->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		
		return $query->execute();
	}
	
	/**
	 * Crée un nouveau paiement
	 *
	 * @param string $paymentId paymentId
	 * @param string $offerType Type d'offre
	 * @param int $serverId ID du serveur cible
	 *
	 * @return bool Résultat
	 */
	public function createPayment(string $paymentId, int $offerType, int $serverId = 0) : bool {
		global $db;
		
		$query = $db->prepare("INSERT INTO users_payments(payment_id, offer_type, server_id) VALUES(:payment_id, :offer_type, :server_id)");
		$query->bindValue(":payment_id", $paymentId, PDO::PARAM_STR);
		$query->bindValue(":offer_type", $offerType, PDO::PARAM_INT);
		$query->bindValue(":server_id", $serverId, PDO::PARAM_INT);
		
		return $query->execute();
	}
	
	/**
	 * Récupère les données à propos d'un paiement
	 *
	 * @param string $paymentId paymentId
	 *
	 * @return array Résultat
	 */
	public function getPaymentData(string $paymentId) : array {
		global $db;
		
		$query = $db->prepare("SELECT offer_type, server_id FROM users_payments WHERE payment_id = :payment_id");
		$query->bindValue(":payment_id", $paymentId, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		if (empty($data)) {
			return [];
		}
		
		$result = [
			"offer_type" => (int)$data["offer_type"],
			"server_id" => (int)$data["server_id"]
		];
		
		$query = $db->prepare("DELETE FROM users_payments WHERE payment_id = :payment_id");
		$query->bindValue(":payment_id", $paymentId, PDO::PARAM_STR);
		$query->execute();
		
		return $result;
	}
	
	/**
	 * Récupère l'ID de l'utilisateur
	 */
	public function getId() : int {
		global $db;
		
		$query = $db->prepare("SELECT id FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["id"];
	}
	
	/**
	 * Crée une facture
	 *
	 * @param int $type Type de serveur
	 * @param float $price Prix
	 */
	public function createInvoice(int $type, float $price) : int {
		global $db;
		
		$query = $db->prepare("INSERT INTO users_invoices(owner, type, price, microtime) VALUES(:owner, :type, :price, ".str_replace(".", "", microtime(1)).")");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->bindValue(":type", $type, PDO::PARAM_INT);
		$query->bindValue(":price", $price, PDO::PARAM_STR);
		$query->execute();
		
		return $db->lastInsertId();
	}
	
	/**
	 * Charge une facture
	 *
	 * @param int $id ID de la facture
	 *
	 * @return array Données à propos de la facture
	 */
	public function getInvoice(int $id) : array {
		global $db;
		
		if (!$_SESSION["admin"]) {
			$query = $db->prepare("SELECT type, price, microtime FROM users_invoices WHERE id = :id AND owner = :owner");
			$query->bindValue(":id", $id, PDO::PARAM_INT);
			$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		} else {
			$query = $db->prepare("SELECT type, price, microtime, owner FROM users_invoices WHERE id = :id");
			$query->bindValue(":id", $id, PDO::PARAM_INT);
		}
		$query->execute();
		$data = $query->fetch();
		
		if (empty($data)) {
			return [];
		}
		
		$data = array_map("trim", $data);
		
		return $data;
	}
	
	/**
	 * Récupère la liste des serveurs de l'utilisateur
	 *
	 * @return array Liste des serveurs de l'utilisateur
	 */
	public function getServersList() : array {
		global $db, $session;
		
		if (!$session["admin"]) {
			$query = $db->prepare("SELECT id, ip, type, expiration FROM servers WHERE owner = :owner ORDER BY id ASC");
			$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		} else {
			$query = $db->prepare("SELECT id, ip, type, expiration FROM servers WHERE expiration > 0 ORDER BY id ASC");
		}
		$query->execute();
		$data = $query->fetchAll();
		if (empty($data)) {
			return [];
		}
		
		$result = [];
		
		foreach ($data as $value) {
			$result[] = [
				"id" => (int)$value["id"],
				"ip" => trim($value["ip"]),
				"type" => (int)$value["type"],
				"expiration" => (int)$value["expiration"]
			];
		}
		
		return $result;
	}
	
	/**
	 * Récupère la liste des factures de l'utilisateur
	 *
	 * @return array Résultat
	 */
	public function getInvoicesList() : array {
		global $db, $session;
		
		if (!$session) {
			$query = $db->prepare("SELECT id, type, price, microtime FROM users_invoices WHERE owner = :owner");
			$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		} else {
			$query = $db->prepare("SELECT id, type, price, microtime FROM users_invoices");
			$query->execute();
		}
		$query->execute();
		$data = $query->fetchAll();
		if (empty($data)) {
			return [];
		}
		
		$result = [];
		
		foreach ($data as $value) {
			$result[] = [
				"id" => (int)$value["id"],
				"type" => (int)$value["type"],
				"price" => (float)$value["price"],
				"timestamp" => (int)substr($value["microtime"], 0, -4)
			];
		}
		
		return $result;
	}
	
	/**
	 * Vérifie si l'utilisateur possède un serveur spécifique
	 *
	 * @param int $serverId ID du serveur
	 *
	 * @return bool Résultat
	 */
	public function hasServer(int $serverId) : bool {
		global $db, $session;
		
		if (!$session["admin"]) {
			$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE id = :id AND owner = :owner");
			$query->bindValue(":id", $serverId, PDO::PARAM_INT);
			$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		} else {
			$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE id = :id AND expiration > 0");
			$query->bindValue(":id", $serverId, PDO::PARAM_INT);
		}
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] == 1;
	}
	
	/**
	 * Charge un ticket
	 *
	 * @return array Données du ticket
	 */
	public function loadTicket() : array {
		global $db;
		
		$query = $db->prepare("SELECT owner, content, timestamp FROM tickets_messages WHERE (owner = '0' AND recipient = :owner) OR (owner = :owner AND recipient = '0') ORDER BY timestamp DESC LIMIT 20");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetchAll();
		$result = [];
		
		foreach ($data as $value) {
			$result[] = [
				"owner" => (string)trim($value["owner"]),
				"content" => (string)trim($value["content"]),
				"timestamp" => (int)$value["timestamp"]
			];
		}
		
		return $result;
	}
	
	/**
	 * Répond à un ticket
	 *
	 * @param string $message Contenu du message
	 *
	 * @return bool Résultat
	 */
	public function replyToTicket($message) : bool {
		global $db;
		
		$query = $db->prepare("INSERT INTO tickets_messages(owner, recipient, content, timestamp) VALUES(:owner, '0', :content, ".time().")");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->bindValue(":content", $message, PDO::PARAM_STR);
		
		return $query->execute();
	}
	
	/**
	 * Vérifie si l'utilisateur est administrateur
	 *
	 * @return bool Résultat
	 */
	public function isAdmin() : bool {
		global $db;
		
		$query = $db->prepare("SELECT admin FROM users WHERE phone = :phone");
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["admin"] == 1;
	}
	
	/**
	 * Vérifie si l'utilisateur possède un serveur gratuit
	 *
	 * @return bool Résultat
	 */
	public function hasFreeServer() : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM servers WHERE owner = :owner");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] >= 1;
	}
	
	/**
	 * Modifie le profil de l'utilisateur
	 *
	 * [...]
	 *
	 * @return bool Résultat
	 */
	public function updateProfile(string $firstname, string $lastname, string $company, string $address1, string $address2, string $city, string $postalcode) {
		global $db;
		
		$query = $db->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, company_name = :company_name, address1 = :address1, address2 = :address2, city = :city, postal_code = :postal_code WHERE phone = :phone");
		$query->bindValue(":first_name", $firstname, PDO::PARAM_STR);
		$query->bindValue(":last_name", $lastname, PDO::PARAM_STR);
		$query->bindValue(":company_name", $company, PDO::PARAM_STR);
		$query->bindValue(":address1", $address1, PDO::PARAM_STR);
		$query->bindValue(":address2", $address2, PDO::PARAM_STR);
		$query->bindValue(":city", $city, PDO::PARAM_STR);
		$query->bindValue(":postal_code", $postalcode, PDO::PARAM_STR);
		$query->bindValue(":phone", $this->phone, PDO::PARAM_STR);
		
		return $query->execute();
	}
	
	/**
	 * Crée une entrée dans les logs de l'utilisateur
	 *
	 * @return bool Résultat
	 */
	public function createLogEntry() {
		global $db;
		
		$query = $db->prepare("INSERT INTO users_logs(owner, ip, port, uri, user_agent, session) VALUES(:owner, :ip, :port, :uri, :user_agent, :session)");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->bindValue(":ip", $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
		$query->bindValue(":port", $_SERVER["REMOTE_PORT"], PDO::PARAM_INT);
		$query->bindValue(":uri", substr($_SERVER["REQUEST_URI"], 0, 255), PDO::PARAM_STR);
		$query->bindValue(":user_agent", isset($_SERVER["HTTP_USER_AGENT"]) ? substr($_SERVER["HTTP_USER_AGENT"], 0, 255) : "", PDO::PARAM_STR);
		$query->bindValue(":session", $_COOKIE["session"], PDO::PARAM_STR);
		
		return $query->execute();
	}
	
	/**
	 * Vérifie si une session existe
	 *
	 * @param string $session Session
	 *
	 * @return bool Résultat
	 */
	public static function sessionExists($session) : bool {
		global $db;
		
		$query = $db->prepare("SELECT COUNT(*) AS nb FROM users_sessions WHERE session = :session");
		$query->bindValue(":session", $session, PDO::PARAM_STR);
		$query->execute();
		$data = $query->fetch();
		
		return $data["nb"] > 0;
	}
	
	public static function getSessionData($session) : array {
		global $db;
		
		$query = $db->prepare("SELECT owner, ip, created, last_seen, has2fa, admin FROM users_sessions WHERE session = :session");
		$query->bindValue(":session", $session, PDO::PARAM_STR);
		$query->execute();
		$data = array_map("trim", $query->fetch());
		
		return $data;
	}
	
	public function createSession(bool $has2fa = true, bool $admin = false) : bool {
		global $db;
		
		$sessionName = sha1(random_bytes(32).microtime(1).$this->phone);
		
		$query = $db->prepare("INSERT INTO users_sessions(owner, session, ip, created, last_seen, has2fa, admin) VALUES(:owner, :session, :ip, ".time().", ".time().", :has2fa, :admin)");
		$query->bindValue(":owner", $this->phone, PDO::PARAM_STR);
		$query->bindValue(":session", $sessionName, PDO::PARAM_STR);
		$query->bindValue(":ip", $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
		$query->bindValue(":has2fa", $has2fa, PDO::PARAM_INT);
		$query->bindValue(":admin", $admin, PDO::PARAM_INT);
		
		setcookie("session", $sessionName, time()+31536000, "/", "", $_SERVER["SERVER_PORT"] == 443, true);
		
		return $query->execute();
	}
	
	public function updateSession() {
		global $db;
		
		$query = $db->prepare("UPDATE users_sessions SET ip = :ip, last_seen = ".time()." WHERE session = :session");
		$query->bindValue(":ip", $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
		$query->bindValue(":session", $_COOKIE["session"], PDO::PARAM_STR);
		
		return $query->execute();
	}
	
	public function deleteSession() {
		global $db;
		
		$query = $db->prepare("DELETE FROM users_sessions WHERE session = :session");
		$query->bindValue(":session", $_COOKIE["session"], PDO::PARAM_STR);
		setcookie("session", null, -1);
		
		return $query->execute();
	}
	
	public function validate2fa() : bool {
		global $db;
		
		$query = $db->prepare("UPDATE users_sessions SET has2fa = 1 WHERE session = :session");
		$query->bindValue(":session", $_COOKIE["session"], PDO::PARAM_STR);
		
		return $query->execute();
	}
}