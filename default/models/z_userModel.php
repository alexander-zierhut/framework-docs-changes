<?php
    /**
     * File that defines the user model
     */

    /**
     * User Model
     * 
     * This model handles database stuff with the focus on user managment.
     * An instance of this class can be acquired with z_framework::getModel("z_user")
     */
    class z_userModel extends z_model {
        
        /**
         * Returns a user row of the database, selected by the users id
         * @param int $userid ID of the user we want the data about
         * @return any[] The dataset
         */
        function getUserById($userid) {
            $query = "SELECT * FROM `z_user` WHERE `id`=?";
            $this->exec($query, "i", $userid);
            
            if ($this->getResult()->num_rows > 0) {
                return $this->getResult()->fetch_assoc(); 
            }
            return false;
        }

        /**
         * Returns all userdata from the database
         * @return any[][] The table as a two dimensional array
         */
        function getUserList() {
            return $this->getFullTable("z_user");
        }

        /**
         * Creates an user account
         * @param string $email Email of the user
         * @param int $language Id of users language
         * @param string $passwordString The raw users password. Not hashed! It will be hashed in this function
         * @return int The id of the new created user
         */
        function add($email, $language, $passwordString = null) {
            $query = "INSERT INTO `z_user`(`email`, `languageId`) VALUES (?,?)";
            $this->exec($query, "ss", $email, $language);
            $insertId = $this->getInsertId();

            //Log
            $this->logActionByCategory("user", "User $email created");

            if ($passwordString !== null) {
                $password = passwordHandler::createPassword($passwordString);
                $this->updatePassword($insertId, $password);
            }

            return $insertId;
        }

        /**
         * Sets the password for a user
         * @param int $id Id of the user which password should be changed
         * @param string $pw The raw unhashed new password
         */
        function updatePassword($id, $pw) {
            $sql = "UPDATE `z_user` SET `password`=?, `Salt`=? WHERE `id`=?";
            $this->exec($sql, "ssi", $pw["hash"], $pw["salt"], $id);
        }

        /**
         * Gets the number of registered users
         * @return int The number of registered users
         */
        function getCount() {
            return $this->countTableEntries("z_user");
        }

        /**
         * Updates the clients settings
         * @param int $id Id of the target user
         * @param string $email The new email
         * @param int $language The new language id
         */
        function updateAccountSettings($id, $email, $language) {
            $query = "UPDATE `z_user` SET `email`=?, `languageId`=? WHERE `id`=?";
            $this->exec($query, "siii", $email, $language, $id);

            //Log
            $this->logAction($this->getLogCategoryIdByName("user"), "User account updated (User ID: $id)", $id);
        }

        /**
         * Gets all the roles a user has
         * @param int $userId The id of the target user
         * @return any[] The datasets of the user_role table
         */
        function getRoles($userId) {
            $sql = "SELECT * FROM z_user_role WHERE user = ? AND active = 1";
            $this->exec($sql, "i", $userId);
            return $this->resultToArray();
        }

        /**
         * Creates a role
         * @return int The id of the new created role
         */
        function createRole() {
            $sql = "INSERT INTO `z_role` () VALUES ()";
            $this->exec($sql);
            return $this->getInsertId();
        }

        /**
         * Deactivates a role
         * 
         * After a role is deactivated the users with it will loose the role specific permissions as long as they don't have another role with these.
         * 
         * @param int $roleId The id of the role to deactivate
         */
        function deactivateRole($roleId) {
            $sql = "UPDATE z_role SET active = 0 WHERE id = ?";
            $this->exec($sql, "i", $roleId);
        }

        /**
         * Gets all permissions a specific user has
         * 
         * @param int $userId Id of the target user
         * @return string[] Array filled with permissions
         */
        function getPermissionsByUserId($userId) {
            $sql = "SELECT p.name FROM z_role_permission p LEFT JOIN z_user_role u ON p.role = u.role WHERE u.active = 1 ANd p.active = 1 AND u.user = ?";
            $this->exec($sql, "i", $userId);
            $arr = $this->resultToArray();
            $out = [];
            foreach ($arr as $perm) {
                $out[] = $perm["name"];
            }
            return $out;
        }

    }
?>