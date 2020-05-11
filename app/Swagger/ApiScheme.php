<?php
/**
 *  //////////////////////////////////////////////////
 *  //////////  Scheme
 *  //////////////////////////////////////////////////
 */

/**
 * @OA\Schema(
 *   schema="int64",
 *   type="integer",
 *   format="int64",
 *   description="The unique identifier of a product in our catalog"
 * )
 */

/**
 * @OA\Schema(
 *   schema="string",
 *   type="string",
 *   description="string scheme"
 * )
 */

/**
 * @OA\Schema(
 *   schema="boolean",
 *   type="boolean",
 *   description="boolean scheme"
 * )
 */

/**
 * @OA\Schema(
 *   schema="ExampleModel",
 *   type="object",
 *   description="example description",
 *   @OA\MediaType(
 *      mediaType="application/json",
 *      @OA\Schema(ref="#/components/schemas/ExampleModel"),
 *   )
 * )
 */


/**
 * @OA\Get(
 *     path="/api/v3/403",
 *     tags={"Response"},
 *     summary="unauthorize",
 *     description="unauthorize exception",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     operationId="matchType",
 *      @OA\Response(
 *          response="403",
 *          description="unauthorize",
 *          @OA\MediaType(
 *          mediaType="application/json",
 *              @OA\Schema(
 *                   @OA\Property(
 *                      property="code",
 *                      type="string",
 *                      description="flybook api result code",
 *                      enum={
 *                        "403",
 *                      },
 *                  ),
 *                   @OA\Property(
 *                      property="message",
 *                      type="string",
 *                      description="flybook api result message",
 *                      enum={
 *                          "인증 오류 입니다",
 *                      },
 *                  ),
 *                   @OA\Property(
 *                      property="data",
 *                      type="object",
 *                      description="flybook api result object",
 *                      enum={
 *                          "",
 *                      },
 *                  ),
 *
 *               )
 *          ),
 *      ),
 * ),
 * )
 */

/**
 * @OA\Get(
 *     path="/api/v3/401",
 *     tags={"Response"},
 *     summary="unauthorize",
 *     description="unauthorize exception",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     operationId="matchType",
 *     @OA\Response(
 *          response="401",
 *          description="unauthorize",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                   @OA\Property(
 *                      property="code",
 *                      type="string",
 *                      description="flybook api result code",
 *                      enum={
 *                        "401",
 *                      },
 *                  ),
 *                   @OA\Property(
 *                      property="message",
 *                      type="string",
 *                      description="flybook api result message",
 *                      enum={
 *                          "인증 오류 입니다",
 *                      },
 *                  ),
 *                   @OA\Property(
 *                      property="data",
 *                      type="objec",
 *                      description="flybook api result object",
 *                      enum={
 *                          "",
 *                      },
 *                  ),
 *               )
 *          ),
 *      ),
 * )
 */
