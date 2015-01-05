<?php

/**
 * Copyright 2013 JervDesign
 * Controller
 *
 * @author James Jervis
 * @interface
 */
interface Controller {

    public function setArgs($val);

    public function getArgs();

    public function actionGet();

    public function actionPost();

    public function actionPut();

    public function actionDelete();
}