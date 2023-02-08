<?php

/**
 *  //////////////////////////////////////////////////
 *  //////////  Information
 *  //////////////////////////////////////////////////
 */

/**
 * @OA\Info(
 *     title="Example API",
 *     version="0.1.1",
 *     @OA\Contact(
 *          email= "WilsonParker@flybook.kr"
 *     )
 * ),
 *
 * @OA\Server(
 *     url="http://local.example.com",
 *     description="local"
 *),
 *
 * @OA\Server(
 *     url="http://test.example.com",
 *     description="test"
 * ),
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     in="header",
 *     securityScheme="bearerAuth"
 * )
 */
