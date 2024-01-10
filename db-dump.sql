--
-- PostgreSQL database dump
--

-- Dumped from database version 13.3
-- Dumped by pg_dump version 14.7 (Homebrew)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: book; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.book (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    image character varying(255),
    authors text,
    publication_date date,
    isbn character varying(13),
    description text,
    user_id integer NOT NULL
);


ALTER TABLE public.book OWNER TO postgres;

--
-- Name: COLUMN book.authors; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.book.authors IS '(DC2Type:simple_array)';


--
-- Name: COLUMN book.publication_date; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.book.publication_date IS '(DC2Type:date_immutable)';


--
-- Name: book_category; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.book_category (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL
);


ALTER TABLE public.book_category OWNER TO postgres;

--
-- Name: book_category_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.book_category_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_category_id_seq OWNER TO postgres;

--
-- Name: book_chapter; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.book_chapter (
    id integer NOT NULL,
    book_id integer NOT NULL,
    parent_id integer,
    slug character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    sort integer DEFAULT 0 NOT NULL,
    level integer NOT NULL
);


ALTER TABLE public.book_chapter OWNER TO postgres;

--
-- Name: book_chapter_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.book_chapter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_chapter_id_seq OWNER TO postgres;

--
-- Name: book_content; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.book_content (
    id integer NOT NULL,
    chapter_id integer NOT NULL,
    content text NOT NULL,
    is_published boolean DEFAULT false NOT NULL
);


ALTER TABLE public.book_content OWNER TO postgres;

--
-- Name: book_content_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.book_content_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_content_id_seq OWNER TO postgres;

--
-- Name: book_format; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.book_format (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    comment character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.book_format OWNER TO postgres;

--
-- Name: book_format_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.book_format_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_format_id_seq OWNER TO postgres;

--
-- Name: book_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.book_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_id_seq OWNER TO postgres;

--
-- Name: book_to_book_category; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.book_to_book_category (
    book_id integer NOT NULL,
    book_category_id integer NOT NULL
);


ALTER TABLE public.book_to_book_category OWNER TO postgres;

--
-- Name: book_to_book_format; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.book_to_book_format (
    id integer NOT NULL,
    book_id integer NOT NULL,
    format_id integer NOT NULL,
    price numeric(10,2) NOT NULL,
    discount_percent integer
);


ALTER TABLE public.book_to_book_format OWNER TO postgres;

--
-- Name: book_to_book_format_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.book_to_book_format_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.book_to_book_format_id_seq OWNER TO postgres;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO postgres;

--
-- Name: refresh_token; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.refresh_token (
    id integer NOT NULL,
    user_id integer NOT NULL,
    refresh_token character varying(255) NOT NULL,
    username character varying(255) NOT NULL,
    valid timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.refresh_token OWNER TO postgres;

--
-- Name: COLUMN refresh_token.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.refresh_token.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: refresh_token_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.refresh_token_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.refresh_token_id_seq OWNER TO postgres;

--
-- Name: review; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.review (
    id integer NOT NULL,
    book_id integer NOT NULL,
    rating integer NOT NULL,
    content text NOT NULL,
    author character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.review OWNER TO postgres;

--
-- Name: COLUMN review.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.review.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: review_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.review_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.review_id_seq OWNER TO postgres;

--
-- Name: subscriber; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subscriber (
    id integer NOT NULL,
    email character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.subscriber OWNER TO postgres;

--
-- Name: COLUMN subscriber.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.subscriber.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: subscriber_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.subscriber_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.subscriber_id_seq OWNER TO postgres;

--
-- Name: user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    roles text NOT NULL
);


ALTER TABLE public."user" OWNER TO postgres;

--
-- Name: COLUMN "user".roles; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public."user".roles IS '(DC2Type:simple_array)';


--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_id_seq OWNER TO postgres;

--
-- Data for Name: book; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.book (id, title, slug, image, authors, publication_date, isbn, description, user_id) FROM stdin;
8	New title of book	New-title-of-book	\N	\N	\N	\N	\N	8
\.


--
-- Data for Name: book_category; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.book_category (id, title, slug) FROM stdin;
\.


--
-- Data for Name: book_chapter; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.book_chapter (id, book_id, parent_id, slug, title, sort, level) FROM stdin;
38	8	\N	Edit-title-of-chapter	Edit title of chapter	1	1
\.


--
-- Data for Name: book_content; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.book_content (id, chapter_id, content, is_published) FROM stdin;
8	38	New content 2 of book chapter	t
7	38	New content 1 of book chapter	t
\.


--
-- Data for Name: book_format; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.book_format (id, title, description, comment) FROM stdin;
\.


--
-- Data for Name: book_to_book_category; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.book_to_book_category (book_id, book_category_id) FROM stdin;
\.


--
-- Data for Name: book_to_book_format; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.book_to_book_format (id, book_id, format_id, price, discount_percent) FROM stdin;
\.


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20230916074610	2023-12-19 13:46:10	15
DoctrineMigrations\\Version20230918075220	2023-12-19 13:46:10	17
DoctrineMigrations\\Version20230919120922	2023-12-19 13:46:10	49
DoctrineMigrations\\Version20231004074837	2023-12-19 13:46:10	12
DoctrineMigrations\\Version20231010102819	2023-12-19 13:46:10	118
DoctrineMigrations\\Version20231106215500	2023-12-19 13:46:10	25
DoctrineMigrations\\Version20231108143645	2023-12-19 13:46:11	15
DoctrineMigrations\\Version20231110082439	2023-12-19 13:46:11	25
DoctrineMigrations\\Version20231124095445	2023-12-19 13:46:11	24
DoctrineMigrations\\Version20231208084622	2023-12-19 13:46:11	4
DoctrineMigrations\\Version20231219134950	2023-12-19 13:49:57	31
DoctrineMigrations\\Version20240105115518	2024-01-05 11:56:11	67
\.


--
-- Data for Name: refresh_token; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.refresh_token (id, user_id, refresh_token, username, valid, created_at) FROM stdin;
8	8	a865220087b948490fbfdd3d442864968af8a6bbd84351a4ce7f0dff6cb5b5e311b5e700bdcd342dcd0fe14d05b3f98289e0dc247d5847010d7d3b67d63c3db8	admin@publisher.com	2024-02-09 12:28:24	2024-01-10 12:28:24
\.


--
-- Data for Name: review; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.review (id, book_id, rating, content, author, created_at) FROM stdin;
\.


--
-- Data for Name: subscriber; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.subscriber (id, email, created_at) FROM stdin;
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public."user" (id, first_name, last_name, email, password, roles) FROM stdin;
8	Taras	Moroz	admin@publisher.com	$2y$13$.k1h6EJRNqadg/4fD5cyn.hSEy0jbOZbxpzK0MO8tsg8V0BSDXFeS	ROLE_AUTHOR
\.


--
-- Name: book_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.book_category_id_seq', 1, false);


--
-- Name: book_chapter_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.book_chapter_id_seq', 38, true);


--
-- Name: book_content_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.book_content_id_seq', 8, true);


--
-- Name: book_format_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.book_format_id_seq', 1, false);


--
-- Name: book_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.book_id_seq', 8, true);


--
-- Name: book_to_book_format_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.book_to_book_format_id_seq', 1, false);


--
-- Name: refresh_token_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.refresh_token_id_seq', 8, true);


--
-- Name: review_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.review_id_seq', 1, false);


--
-- Name: subscriber_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.subscriber_id_seq', 1, false);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_id_seq', 8, true);


--
-- Name: book_category book_category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_category
    ADD CONSTRAINT book_category_pkey PRIMARY KEY (id);


--
-- Name: book_chapter book_chapter_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_chapter
    ADD CONSTRAINT book_chapter_pkey PRIMARY KEY (id);


--
-- Name: book_content book_content_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_content
    ADD CONSTRAINT book_content_pkey PRIMARY KEY (id);


--
-- Name: book_format book_format_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_format
    ADD CONSTRAINT book_format_pkey PRIMARY KEY (id);


--
-- Name: book book_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book
    ADD CONSTRAINT book_pkey PRIMARY KEY (id);


--
-- Name: book_to_book_category book_to_book_category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_to_book_category
    ADD CONSTRAINT book_to_book_category_pkey PRIMARY KEY (book_id, book_category_id);


--
-- Name: book_to_book_format book_to_book_format_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_to_book_format
    ADD CONSTRAINT book_to_book_format_pkey PRIMARY KEY (id);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: refresh_token refresh_token_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.refresh_token
    ADD CONSTRAINT refresh_token_pkey PRIMARY KEY (id);


--
-- Name: review review_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_pkey PRIMARY KEY (id);


--
-- Name: subscriber subscriber_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subscriber
    ADD CONSTRAINT subscriber_pkey PRIMARY KEY (id);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: idx_57511be216a2b381; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_57511be216a2b381 ON public.book_to_book_category USING btree (book_id);


--
-- Name: idx_57511be240b1d29e; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_57511be240b1d29e ON public.book_to_book_category USING btree (book_category_id);


--
-- Name: idx_6aa19db816a2b381; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_6aa19db816a2b381 ON public.book_chapter USING btree (book_id);


--
-- Name: idx_6aa19db8727aca70; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_6aa19db8727aca70 ON public.book_chapter USING btree (parent_id);


--
-- Name: idx_6de5183f579f4768; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_6de5183f579f4768 ON public.book_content USING btree (chapter_id);


--
-- Name: idx_794381c616a2b381; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_794381c616a2b381 ON public.review USING btree (book_id);


--
-- Name: idx_c74f2195a76ed395; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_c74f2195a76ed395 ON public.refresh_token USING btree (user_id);


--
-- Name: idx_cbe5a331a76ed395; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_cbe5a331a76ed395 ON public.book USING btree (user_id);


--
-- Name: idx_d02de22216a2b381; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_d02de22216a2b381 ON public.book_to_book_format USING btree (book_id);


--
-- Name: idx_d02de222d629f605; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_d02de222d629f605 ON public.book_to_book_format USING btree (format_id);


--
-- Name: uniq_8d93d649e7927c74; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON public."user" USING btree (email);


--
-- Name: book_to_book_category fk_57511be216a2b381; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_to_book_category
    ADD CONSTRAINT fk_57511be216a2b381 FOREIGN KEY (book_id) REFERENCES public.book(id) ON DELETE CASCADE;


--
-- Name: book_to_book_category fk_57511be240b1d29e; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_to_book_category
    ADD CONSTRAINT fk_57511be240b1d29e FOREIGN KEY (book_category_id) REFERENCES public.book_category(id) ON DELETE CASCADE;


--
-- Name: book_chapter fk_6aa19db816a2b381; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_chapter
    ADD CONSTRAINT fk_6aa19db816a2b381 FOREIGN KEY (book_id) REFERENCES public.book(id);


--
-- Name: book_chapter fk_6aa19db8727aca70; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_chapter
    ADD CONSTRAINT fk_6aa19db8727aca70 FOREIGN KEY (parent_id) REFERENCES public.book_chapter(id);


--
-- Name: book_content fk_6de5183f579f4768; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_content
    ADD CONSTRAINT fk_6de5183f579f4768 FOREIGN KEY (chapter_id) REFERENCES public.book_chapter(id);


--
-- Name: review fk_794381c616a2b381; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.review
    ADD CONSTRAINT fk_794381c616a2b381 FOREIGN KEY (book_id) REFERENCES public.book(id);


--
-- Name: refresh_token fk_c74f2195a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.refresh_token
    ADD CONSTRAINT fk_c74f2195a76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: book fk_cbe5a331a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book
    ADD CONSTRAINT fk_cbe5a331a76ed395 FOREIGN KEY (user_id) REFERENCES public."user"(id);


--
-- Name: book_to_book_format fk_d02de22216a2b381; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_to_book_format
    ADD CONSTRAINT fk_d02de22216a2b381 FOREIGN KEY (book_id) REFERENCES public.book(id);


--
-- Name: book_to_book_format fk_d02de222d629f605; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.book_to_book_format
    ADD CONSTRAINT fk_d02de222d629f605 FOREIGN KEY (format_id) REFERENCES public.book_format(id);


--
-- PostgreSQL database dump complete
--

