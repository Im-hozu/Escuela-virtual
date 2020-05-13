CREATE DATABASE IF NOT EXISTS proyecto_clases;
USE proyecto_clases;

CREATE TABLE users(
    id int(255) auto_increment not null,
    role varchar(20),
    name varchar(255),
    surname varchar(255),
    password varchar(255),
    email varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    remember_token varchar(255),
    CONSTRAINT pk_users PRIMARY KEY(id)

)ENGINE=InnoDb;

CREATE TABLE messages(
    id int(255) auto_increment not null,
    sender int(255) not null,
    addressee int(255)not null,
    body varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_messages PRIMARY KEY(id),
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender) REFERENCES users(id),
    CONSTRAINT fk_messages_addressee FOREIGN KEY (addressee) REFERENCES users(id)
)ENGINE=InnoDb;

CREATE TABLE curses(
    id int(255) auto_increment not null,
    status varchar(20),
    theme varchar(20),
    title varchar(255),
    description varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_curses PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE enrollments(
    id int(255) auto_increment not null,
    user_id int(255) not null,
    curse_id int(255) not null,
    role varchar(20),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_enrollments PRIMARY KEY(id),
    CONSTRAINT fk_enrollments_users FOREIGN KEY(user_id) REFERENCES users(id),
    CONSTRAINT fk_enrollments_curses FOREIGN KEY(curse_id) REFERENCES curses(id)
)ENGINE=InnoDb;

CREATE TABLE sections(
    id int(255) auto_increment not null,
    curse_id int(255) not null,
    status varchar(20),
    title varchar(255),
    description varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_sections PRIMARY KEY(id),
    CONSTRAINT fk_sections_curses FOREIGN KEY(curse_id) REFERENCES curses(id)
)ENGINE=InnoDb;

CREATE TABLE files(
    id int(255) auto_increment not null,
    user_id int(255) not null,
    path varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_files PRIMARY KEY(id),
    CONSTRAINT fk_files_users FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;

CREATE TABLE videos(
    id int(255) auto_increment not null,
    file_id int(255) not null,
    section_id int(255)not null,
    title varchar(255),
    description varchar(255),
    status varchar(20),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_videos PRIMARY KEY(id),
    CONSTRAINT fk_videos_files FOREIGN KEY(file_id) REFERENCES files(id),
    CONSTRAINT fk_videos_sections FOREIGN KEY (section_id) REFERENCES sections(id)
)ENGINE=InnoDb;

CREATE TABLE comments(
    id int(255) auto_increment not null,
    video_id int(255)not null,
    user_id int(255)not null,
    body varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_comments PRIMARY KEY(id),
    CONSTRAINT fk_comments_videos FOREIGN KEY (video_id) REFERENCES videos(id),
    CONSTRAINT fk_comments_users FOREIGN KEY (user_id) REFERENCES users(id) 
)ENGINE=InnoDb;

CREATE TABLE recurses(
    id int(255) auto_increment not null,
    file_id int(255)not null,
    section_id int(255)not null,
    title varchar(255),
    description varchar(255),
    status varchar(20),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_recurses PRIMARY KEY(id),
    CONSTRAINT fk_recurses_files FOREIGN KEY(file_id) REFERENCES files(id),
    CONSTRAINT fk_recurses_sections FOREIGN KEY(section_id) REFERENCES sections(id)
)ENGINE=InnoDb;


CREATE TABLE tasks(
    id int(255) auto_increment not null,
    user_id int(255)not null,
    section_id int(255)not null,
    title varchar(255),
    description varchar(255),
    status varchar(20),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    finish_at datetime DEFAULT NULL,
    CONSTRAINT pk_tasks PRIMARY KEY(id),
    CONSTRAINT fk_tasks_users FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_tasks_sections FOREIGN KEY (section_id) REFERENCES sections(id)
    )ENGINE=InnoDb;

CREATE TABLE delivers(
    id int(255) auto_increment not null,
    user_id int(255)not null,
    task_id int(255)not null,
    path varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_delivers PRIMARY KEY(id),
    CONSTRAINT fk_delivers_users FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_delivers_tasks FOREIGN KEY (task_id) REFERENCES tasks(id)
)ENGINE=InnoDb;

CREATE TABLE tasksfiles(
     id int(255) auto_increment not null,
    task_id int(255) not null,
    file_id int(255) not null,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_tasksfiles PRIMARY KEY(id),
    CONSTRAINT fk_tasksfiles_tasks FOREIGN KEY(task_id) REFERENCES tasks(id),
    CONSTRAINT fk_tasksfiles_files FOREIGN KEY(file_id) REFERENCES files(id)
)ENGINE=InnoDb;

